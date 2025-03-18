<?php
namespace Cookie;

require_once 'util.php';

const MAX_COOKIE_TIMESTAMP = 253402300799;  // 9999-12-31T23:59:59+00:00

final readonly class RecentOffers
{
    private const NAME              = 'recent_offers';
    private const MAX_RECENT_OFFERS = 3;

    /**
     * @return int[]
     */
    static function get(): array
    {
        return mapnull($_COOKIE[self::NAME] ?? null, fn($value) => array_map(intval(...), explode(',', $value))) ?? [];
    }

    static function add(int $offer_id): void
    {
        $value = self::get();
        if (($i_offer_id = array_search($offer_id, $value, true)) !== false) {
            array_pop_key($value, $i_offer_id);
        } elseif (count($value) === self::MAX_RECENT_OFFERS) {
            array_pop($value);
        }
        array_unshift($value, $offer_id);
        notfalse(setcookie(self::NAME, implode(',', $value), MAX_COOKIE_TIMESTAMP, '/'));
    }
}

final readonly class CommentLikes
{
    private const NAME              = 'comment_likes';
    private const MAX_RECENT_OFFERS = 3;

    static function likes(int $comment_id): ?bool
    {
        return self::get_value()[$comment_id] ?? null;
    }

    /**
     * @return array<int, bool>
     */
    private static function get_value(): array
    {
        return mapnull($_COOKIE[self::NAME] ?? null, unserialize(...)) ?? [];
    }

    static function set(int $comment_id, bool $like): void
    {
        $value = self::get_value();
        if (($value[$comment_id] ?? null) === $like) return;
        $value[$comment_id] = $like;
        self::set_value($value);
    }

    static function unset(int $comment_id): void
    {
        $value = self::get_value();
        if (!isset($value[$comment_id])) return;
        unset($value[$comment_id]);
        self::set_value($value);
    }

    /**
     * @param array<int, bool> $value
     */
    private static function set_value(array $value): void
    {
        notfalse(setcookie(self::NAME, serialize($value), MAX_COOKIE_TIMESTAMP, '/'));
    }
}
