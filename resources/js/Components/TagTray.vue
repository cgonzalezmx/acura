<script setup lang="ts">
import { h, ref, useTemplateRef } from 'vue';
import Button from 'primevue/button';
import { useHiddenMarker } from '@/composables/useHiddenMarker';

interface Item {
    label: string;
    [key:string]: any;
}

interface Props {
    items: Item[];
    tagsPrefix?: string;
}

interface Events {
    addTag: [];
    removeTag: [value: number];
    tagSelected: [value: number];
}

enum Direction {
    Left = -1,
    Right = 1
}

const emit = defineEmits<Events>();
const { items } = defineProps<Props>();
const root = useTemplateRef('root');
const container = useTemplateRef('container');
const [leftMarker, isLeftMarkerVisible] = useHiddenMarker({ root });
const [rightMarker, isRightMarkerVisible] = useHiddenMarker({ root, rootMargin: '0px 0px 0px -1px'})
const activeIndex = ref<number | null>(0);

const TrayControl = (props: { icon: string }) => h(Button, {
    icon: props.icon,
    rounded: true,
    outlined: true,
    size: 'small',
    class: 'flex-shrink-0'
});

function scroll(direction: Direction) {
    const scrollSize = container.value?.clientWidth ?? 0;
    root.value?.scrollBy({ left: scrollSize * (direction), behavior: 'smooth'});
}

function selectTag(index: number) {
    activeIndex.value = index;
    emit('tagSelected', index);
}

function adjustActiveIndexAfterRemoval(removedIndex: number) {
    if (activeIndex.value) {
        if (removedIndex < activeIndex.value) {
            activeIndex.value -= 1;
        }
        else if (activeIndex.value === removedIndex) {
            activeIndex.value = null;
        };
    }
}

function removeTag(index: number) {
    emit('removeTag', index);
    adjustActiveIndexAfterRemoval(index);
}
</script>

<template>
    <div class="flex overflow-hidden" ref="container">
        <TrayControl v-if="!isLeftMarkerVisible" icon="fa-solid fa-chevron-left" class="mr-1" @click="scroll(Direction.Left)"/>
        <div class="flex gap-1 overflow-x-auto relative scroll-hidden" ref="root">
            <div ref="leftMarker" class="w-[1px] h-[1px] absolute left-0"></div>
            <div
                v-for="(tag, index) in items"
                :key="tag.label"
                @click="selectTag(index)"
                :class="activeIndex === index ?
                    'bg-slate-700 text-surface-100 border-surface-900' :
                    'border-surface-300 bg-surface-100 hover:bg-surface-200'"
                class="flex-shrink-0 border cursor-pointer px-2 py-1 rounded">
                {{ tagsPrefix + tag.label }}
                <i v-if="tag !== items[0]" class="fa-solid fa-xmark" @click.stop="removeTag(index)"></i>
            </div>
            <div ref="rightMarker" class="w-[1px] h-[1px]"></div>
        </div>
        <TrayControl icon="fa-solid fa-plus" @click="$emit('addTag')" class="ml-1"/>
        <TrayControl v-if="!isRightMarkerVisible" icon="fa-solid fa-chevron-right" class="ml-1" @click="scroll(Direction.Right)"/>
    </div>
</template>