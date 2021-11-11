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
                'cleanse_thumbnail_uri',
                [$this, 'cleanseThumbnailUri'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'make_apps_box',
                [$this, 'makeAppsBox'],
                ['is_safe' => ['html']]
            ),

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

    public function cleanseThumbnailUri(?string $uri): ?string
    {
        if (null === $uri || 0 !== strpos($uri, 'ipfs://')) {
            return $uri;
        }

        return 'https://ipfs.io/ipfs/'.substr($uri, 7);
    }

    public function makeAppsBox(
        array $apps,
        string $identifier,
        string $symbol
    ): string {
        $amms = array_filter(
            $apps,
            fn (array $app): bool => 'AMM' === $app['type'] &&
                'LB' !== $app['name']
        );

        $html = '';

        if (\count($amms) > 0) {
            $html .= '<ul class="text-white">';
        }
        foreach ($amms as $amm) {
            $links = '';
            if ('QUIPUSWAP' === $amm['name']) {
                $links .= sprintf(
                    '<a href="https://quipuswap.com/swap?to=%1$s" class="amm-buy" target="_new">BUY</a> / <a href="https://quipuswap.com/swap?from=%1$s" class="amm-sell" target="_new">SELL</a>',
                    $identifier
                );
            }
            if ('PLENTY' === $amm['name']) {
                $links .= sprintf(
                    '<a href="https://www.plentydefi.com/swap?from=PLENTY&to=%s" class="%s" target="_new">%s</a>',
                    $symbol,
                    'PLENTY' === $symbol ? 'amm-sell' : 'amm-buy',
                    'PLENTY' === $symbol ? 'SELL' : 'BUY'
                );
            }

            $html .= sprintf('<li>Trade on %s: %s</li>', $amm['name'], $links);
        }
        if (\count($amms) > 0) {
            $html .= '</ul>';
        }

        return $html;
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
                $this->cleanseUrl($token->getDiscordLink()),
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

    private function cleanseUrl(string $url): string
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
