<template lang="pug">
.wrapper
  header
    h1 形態素分析器
    .btns
      button.btn.btn-primary(@click="togglePage(1)") 分析画面
      button.btn.btn-primary.mx-1(@click="togglePage(2)") 分析履歴一覧
  main
    Transition(name="shift", mode="out-in")
      Analyse(
        v-if="page === 1",
        ref="analysePage",
        :cache="cache",
        @startLoading="startLoading",
        @endLoading="endLoading",
        @clearCache="clearCache"
      )
      List(
        v-else,
        @startLoading="startLoading",
        @endLoading="endLoading",
        @acceptRow="acceptTextArea"
      )
    Loading(v-if="loading")
</template>

<script>
// vue
import { ref } from "vue";
// components
import Loading from "@/components/Loading";
import Analyse from "@/components/Analyse";
import List from "@/components/List";

export default {
  name: "App",
  components: {
    Analyse,
    List,
    Loading,
  },
  setup() {
    const page = ref(1);
    const loading = ref(false);
    const analysePage = ref(null);
    const cache = ref(null);

    const startLoading = () => (loading.value = true);
    const endLoading = () => (loading.value = false);
    const clearCache = () => (cache.value = null);
    const togglePage = (to) => {
      if (to === 2) {
        cache.value = analysePage.value.text;
      }
      page.value = to;
    };
    const acceptTextArea = async (value) => {
      cache.value = value;
      togglePage(1);
    };

    return {
      page,
      loading,
      cache,
      analysePage,
      togglePage,
      startLoading,
      endLoading,
      acceptTextArea,
      clearCache,
    };
  },
};
</script>
