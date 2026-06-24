/**
 * Curated list icon keys — keep in sync with config/list_icons.php labels.
 * Each key maps to a single emoji shown on list cards in the app.
 */
export const LIST_ICON_EMOJI = {
    film: '🎬',
    ghost: '👻',
    skull: '💀',
    pumpkin: '🎃',
    knife: '🔪',
    spider: '🕷️',
    moon: '🌙',
    fire: '🔥',
    bolt: '⚡',
    heart: '❤️',
    star: '⭐',
    popcorn: '🍿',
    rocket: '🚀',
    clown: '🤡',
    detective: '🕵️',
    laugh: '😂',
    family: '👨‍👩‍👧',
    sword: '⚔️',
    war: '🪖',
    musical: '🎵',
    tv: '📺',
};

export const DEFAULT_LIST_ICON = 'film';

export function listIconEmoji(icon) {
    return LIST_ICON_EMOJI[icon] ?? LIST_ICON_EMOJI[DEFAULT_LIST_ICON];
}
