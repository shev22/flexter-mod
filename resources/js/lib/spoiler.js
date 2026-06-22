/** Whether media overviews should be blurred for spoiler-free mode. */
export function shouldSpoilerBlur(settings) {
    return settings?.spoiler_free === true;
}
