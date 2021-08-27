<template lang="pug">
Card#chart-card
  .flex
    p.title 形態素分析集計
    .filter
      span.filter__adder(
        :class="`${filter.active ? 'active' : ''}`",
        :style="filterStyle"
      )
        span(v-if="!filter.active", @click="filterToggle(true)") FILTER
        .filter__adder--wrapper(v-else)
          FormInput(v-model="filter.surface", label="部分一致")
          FormSelect(v-model="filter.pos", :options="filter.options", label="品詞")
          FormInput(v-model="filter.pronunciation", label="読み")
          .flex
            button.btn.btn-default(@click="filterToggle(false)") キャンセル
            button.btn.btn-primary.mx-1(@click="filterClear") フィルタリング解除
            button.btn.btn-primary(@click="fetchWordData") フィルタリング
  canvas#chart(ref="chart")
</template>

<script>
// vue
import { ref, reactive, computed, onMounted } from "vue";
// utils
import { api } from "@/utils";
// components
import Card from "@/components/Card";
import FormInput from "@/components/FormInput";
import FormSelect from "@/components/FormSelect";
// dependencies
import Chart from "chart.js/auto";

export default {
  name: 'Chart', 
  components: {
    Card,
    FormInput,
    FormSelect,
  },
  setup(_, ctxt) {
    // chart.js用
    const chart = ref(null);
    const chartInstance = ref(null);
    const chartData = reactive({
      data: [],
    });

    // フィルタリングステータス
    const filter = reactive({
      active: false,
      options: [],
      surface: "",
      pos: "",
      pronunciation: "",
    });

    // フィルターカード表示時スタイル
    const filterStyle = computed(() => {
      return filter.active ? { width: "500px", height: "auto" } : {};
    })
    
    // フィルターカード表示制御
    const filterToggle = (value) => {
      filter.active = value;
    };

    // クリア
    const filterClear = async () => {
      filter.surface = ''
      filter.pos = ''
      filter.pronunciation = ''
      await fetchWordData()
    }
  
    // チャート表示用集計データの取得
    const fetchWordData = async () => {
      ctxt.emit("startLoading");
      try {
        const data = new URLSearchParams();
        if (filter.surface) {
          data.append("surface", filter.surface);
        }
        if (filter.pos) {
          data.append("pos", filter.pos);
        }
        if (filter.pronunciation) {
          data.append("pronunciation", filter.pronunciation);
        }
        const [words, pos] = await Promise.all([
          api.post("/words", data),
          api.post("/words/pos", data)
        ]);
        if (words.data) {
          chartData.data.splice(0);
          chartData.data.push(...words.data);
          render();
        }
        if (pos.data) {
          const data = pos.data.map((datum) => {
            return {
              label: `${datum.pos} (${datum.cnt})`,
              value: datum.pos
            }
          })
          filter.options.splice(0)
          filter.options.push(...data)
        }
        console.log(pos)
      } catch (e) {
        console.error(e);
      } finally {
        filterToggle(false)
        ctxt.emit("endLoading");
      }
    };
 
    // チャートの描画
    const render = () => {
      if (chartInstance.value) {
        chartUpdate();
      } else {
        chartInit();
      }
    };

    // チャート更新
    const chartUpdate = () => {
      const chart = chartInstance.value;
      chart.destroy()
      chartInit()
    };

    // チャートの初期化処理
    const chartInit = () => {
      const labels = chartData.data.map((item) => item.surface_form);
      const data = chartData.data.map((item) => item.cnt);
      chartInstance.value = new Chart(chart.value, {
        type: "pie",
        data: {
          labels: labels,
          datasets: [
            {
              label: "ワードリスト",
              data: data,
              backgroundColor: [
                "rgba(233, 184,184, 0.5)",
                "rgba(234, 189, 170, 0.5",
                "rgba(239, 213, 174, 0.5)",
                "rgba(225, 218, 173, 0.5)",
                "rgba(166,208,171,0.5)",
                "rgba(158, 205, 201, 0.5)",
                "rgba(166, 198, 213, 0.5)",
                "rgba(172,178, 205, 0.5)",
                "rgba(192,177,199,0.5)",
                "rgba(217,185,199,0.5)",
              ],
              borderColor: [
                "rgba(233, 184,184, 1)",
                "rgba(234, 189, 170, 1",
                "rgba(239, 213, 174, 1)",
                "rgba(225, 218, 173, 1)",
                "rgba(166,208,171,1)",
                "rgba(158, 205, 201, 1)",
                "rgba(166, 198, 213, 1)",
                "rgba(172,178, 205, 1)",
                "rgba(192,177,199,1)",
                "rgba(217,185,199,1)",
              ],
              borderWidth: 1,
            },
          ],
        },
        options: {
responsive: true,
          plugins: {
            legend: {
              position: "top",
            },
            title: {
              display: true,
              text: "ワード集計結果",
            },
          },
        },
      });
    };
    
    onMounted(async () => {
      await fetchWordData()
    });
    
    return {
      filter,
      chart,
      filterStyle,
      filterToggle,
      filterClear,
      fetchWordData,
    };
  }
}
</script>

<style scoped>
#chart-card {
  max-width: 700px;
  margin: 0 auto;
}
</style>