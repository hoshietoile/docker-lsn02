<template lang="pug">
.list
  Card.mx-1
    p.title 分析履歴一覧
    .my-1
      TransitionGroup.custom-list(name="list", tag="ul")
        li(v-for="(t, idx) in data", :key="idx", @click="accept(t)") {{ t.sentence }}
</template>

<script>
// vue
import { reactive, toRefs, onMounted } from "vue";
// utils
import { api } from "@/utils";
// components
import Card from "@/components/Card";
import FormInput from "@/components/FormInput";
import FormSelect from "@/components/FormSelect";

export default {
  name: "List",
  components: {
    Card,
    FormInput,
    FormSelect,
  },
  emits: ["startLoading", "endLoading", "acceptRow", "togglePage"],
  setup(_, ctxt) {
    const list = reactive({
      data: [],
    });

    // 一覧取得
    const fetchList = async () => {
      ctxt.emit("startLoading");
      try {
        const result = await api.get("/sentences");
        if (result.data) {
          list.data.splice(0);
          list.data.push(...result.data);
        }
      } catch (e) {
        console.error(e);
      } finally {
        ctxt.emit("endLoading");
      }
    };

    const accept = (target) => {
      ctxt.emit("togglePage");
      ctxt.emit("acceptRow", target.sentence);
    };

    onMounted(async () => {
      await fetchList();
    });

    return {
      ...toRefs(list),
      accept,
    };
  },
};
</script>