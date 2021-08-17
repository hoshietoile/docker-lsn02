<template lang="pug">
.form-control(:class="isInvalid ? 'invalid' : ''")
  label(v-if="label.length > 0" :for="id") {{ label }}
  select.custom-input(:id="id" :value="modelValue" @change="$emit('update:modelValue', $event.target.value)")
    option(v-for="(option, idx) in options" :key="idx" :value="option.value") {{ option.label }}
  .invalid-feedback {{ feedback }}
</template>

<script>
import { toRefs, computed } from 'vue'
export default {
  name: 'FormText',
  props: {
    id: {
      type: String,
      default: null
    },
    label: {
      type: String,
      default: ''
    },
    modelValue: {
      type: String,
      required: true
    },
    feedback: {
      type: String,
      default: ''
    },
    options: {
      type: Array,
      required: true
    }
  },
  emits: ['update:modelValue'],
  setup(_props) {
    const props = toRefs(_props)
    const isInvalid = computed(() => {
      return props.feedback.value.length > 0
    })

    return {
      ...props,
      isInvalid
    }
  }
}
</script>
