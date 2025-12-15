<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Exception\SignatureVerificationException;

class CheckoutController extends Controller
{
    public function index(): Response
    {
        $packages = Package::active()
            ->ordered()
            ->get()
            ->map(fn ($package) => [
                'id' => $package->id,
                'slug' => $package->slug,
                'name' => $package->name,
                'description' => $package->description,
                'credits' => $package->credits,
                'price' => $package->price,
                'is_popular' => $package->is_popular,
                'discount_percentage' => $package->discount_percentage,
                'original_price' => $package->original_price,
            ]);

        return Inertia::render('checkout/Index', [
            'packages' => $packages,
        ]);
    }

    public function create(Request $request, string $packageSlug): JsonResponse
    {
        $package = Package::active()->where('slug', $packageSlug)->first();

        if (! $package) {
            return response()->json([
                'error' => 'Invalid package selected.',
            ], 400);
        }

        $user = $request->user();

        // Use the Stripe Price ID from the package
        $checkout = $user->checkout(
            [
                [
                    'price' => $package->stripe_price_id,
                    'quantity' => 1,
                ],
            ],
            [
                'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.index'),
                'metadata' => [
                    'package_id' => $package->id,
                    'credits' => $package->credits,
                ],
            ]
        );

        return response()->json([
            'url' => $checkout->url,
        ]);
    }

    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid checkout session.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Payment successful! Your credits will be added shortly.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Webhook error: Invalid payload', ['error' => $e->getMessage()]);

            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook error: Invalid signature', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        // Handle the checkout.session.completed event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $this->handleCheckoutSessionCompleted($session);
        }

        return response('Webhook handled', 200);
    }

    private function handleCheckoutSessionCompleted($session): void
    {
        // Get the customer ID from the session
        $customerId = $session->customer;

        // Find the user by Stripe customer ID
        $user = User::where('stripe_id', $customerId)->first();

        if (! $user) {
            Log::error('User not found for Stripe customer', ['customer_id' => $customerId]);

            return;
        }

        // Get package and credits from metadata
        $packageId = $session->metadata->package_id ?? null;
        $credits = $session->metadata->credits ?? null;

        if (! $credits || ! $packageId) {
            Log::error('Missing metadata in session', [
                'session_id' => $session->id,
                'package_id' => $packageId,
                'credits' => $credits,
            ]);

            return;
        }

        // Find the package for better logging
        $package = Package::find($packageId);
        $packageName = $package ? $package->name : 'Unknown';

        // Add credits to user
        $user->creditAdd((int) $credits, "Purchased {$packageName} package", [
            'stripe_session_id' => $session->id,
            'package_id' => $packageId,
            'tags' => ['purchase'],
        ]);

        Log::info('Credits added to user', [
            'user_id' => $user->id,
            'credits' => $credits,
            'package_id' => $packageId,
            'package_name' => $packageName,
        ]);
    }
}
