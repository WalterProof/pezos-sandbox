<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig;

use App\Entity\Contract;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @final
 * @experimental
 */
class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'make_token_link',
                [$this, 'makeTokenLink'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'make_social_links',
                [$this, 'makeSocialLinks'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function makeTokenLink(Contract $token): string
    {
        return $token->getWebsiteLink()
            ? $this->makeLink($token->getWebsiteLink(), $token->getSymbol())
            : $token->getSymbol();
    }

    public function makeSocialLinks(Contract $token): string
    {
        $links = [];

        if ($token->getDiscordLink()) {
            $links[] = $this->makeLink(
                $this->fixUrl($token->getDiscordLink()),
                'Discord'
            );
        }

        if ($token->getTelegramLink()) {
            $links[] = $this->makeLink($token->getTelegramLink(), 'Telegram');
        }

        if ($token->getTwitterLink()) {
            $links[] = $this->makeLink($token->getTwitterLink(), 'Twitter');
        }

        return \count($links) > 0
            ? 'Socialize on '.implode(', ', $links)
            : '';
    }

    private function fixUrl(string $url): string
    {
        if (0 !== strpos($url, 'http://') || 0 !== strpos($url, 'https://')) {
            return 'https://'.$url;
        }

        return $url;
    }

    private function makeLink(string $url, string $label)
    {
        return sprintf('<a href="%s">%s</a>', $url, $label);
    }
}
