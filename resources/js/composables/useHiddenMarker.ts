import { set, useIntersectionObserver, UseIntersectionObserverOptions } from "@vueuse/core";
import { ref } from "vue";

export function useHiddenMarker(options: UseIntersectionObserverOptions) {
    const hiddenMarker = ref<HTMLElement | null>(null);
    const isVisible = ref(false);

    useIntersectionObserver(
        hiddenMarker,
        ([{isIntersecting}]) => set(isVisible, isIntersecting),
        options
    );

    return [
        hiddenMarker,
        isVisible
    ]
}