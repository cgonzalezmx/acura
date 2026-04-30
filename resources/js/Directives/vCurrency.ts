import { currency } from '@/utils/formatters';
import { DirectiveBinding } from 'vue';

const asCurrency = (input: any) => isNaN(input) ? 'NaN' : currency(input);

export default {
    mounted(element: HTMLElement, binding: DirectiveBinding) {
        element.innerHTML = asCurrency(binding.value);
    },
    updated(element: HTMLElement, binding: DirectiveBinding) {
        element.innerHTML = asCurrency(binding.value);
    }
}