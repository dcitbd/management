<?php
declare(strict_types=1);

namespace App\Services\Messaging;

interface ChannelProviderInterface
{
    public function getProviderSlug(): string;
    public function getDisplayName(): string;
    public function isPublicApiAvailable(): bool;
    public function testConnection(array $credentials): array;
    public function sendMessage(string $recipientId, string $messageText, array $credentials): array;
    public function verifyWebhookSignature(string $payload, string $signatureHeader, string $secret): bool;
}
