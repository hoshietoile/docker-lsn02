<template lang="pug">
.card(:class="type === 'flat' ? 'flat' : ''")
  slot
</template>

<script>
import { toRefs } from 'vue'
export default {
  name: "Card",
  props: {
    type: {
      type: String,
      default: ''
    }
  },
  setup(_props) {
    return {
      ...toRefs(_props)
    }
  }
}
</script>