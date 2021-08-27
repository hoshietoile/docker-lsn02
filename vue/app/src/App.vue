<template lang="pug">
.wrapper
  header
    h1 形態素分析器
    .btns
      button.btn.btn-primary(@click="togglePage(1)") 分析画面
      button.btn.btn-primary.mx-1(@click="togglePage(2)") 分析履歴一覧
      button.btn.btn-primary(@click="togglePage(3)") 集計結果
  main
    //- TransitionGroup(name="shift", tag="li" mode="out-in")
    Analyse(
      v-if="page === 1",
      ref="analysePage",
      :cache="cache",
      @startLoading="startLoading",
      @endLoading="endLoading",
      @clearCache="clearCache"
    )
    List(
      v-if="page === 2",
      @startLoading="startLoading",
      @endLoading="endLoading",
      @acceptRow="acceptTextArea"
    )
    Chart(
      v-if="page === 3"
      @startLoading="startLoading",
      @endLoading="endLoading",
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
import Chart from "@/components/Chart"

export default {
  name: "App",
  components: {
    Analyse,
    List,
    Chart,
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
      if (page.value === 1) {
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
