const clickOutside = {
    beforeMount(el, binding) {
        el.clickOutsideEvent = function(event) {

            const searchInput = document.getElementById('default-search');
            if (!el.contains(event.target) && !(searchInput?.contains(event.target))) {
                binding.value(event);
            }
        };
        document.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
    }
};

export { clickOutside };
