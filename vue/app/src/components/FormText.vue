<template lang="pug">
.form-control(:class="isInvalid ? 'invalid' : ''")
  label(:for="id") {{ label }}
  textarea.custom-input(:id="id" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)", rows="5")
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
