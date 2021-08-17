<template lang="pug">
.analyse
  Card
    FormText#text(v-model="text", label="分析対象文字列", :feedback="feedback")
    button.btn.btn-default(@click="clear") クリア
    button.btn.btn-primary.mx-1(@click="store") 保存
    button.btn.btn-primary(@click="exec") 分析実行
  Transition(name="drop", mode="out-in")
    Card.my-1(v-if="token.length > 0")
      p.title 分析結果
      .my-1
        Card(type="flat")
          TransitionGroup.custom-list.flex.flex-wrap(name="list", tag="ul")
            li.token-container(v-for="(t, idx) in token", :key="idx") {{ t.surface_form }}
              .token-detail
                p 基本形 {{ t.basic_form }}
                p 活用 {{ t.conjugated_type }}
                p 品詞 {{ t.pos }}
                p 発音 {{ t.pronunciation }}
                p 読み {{ t.reading }}
</template>

<script>
// vue
import { reactive, toRefs, watch, onMounted } from "vue";
// utils
import { analyse, api } from "@/utils";
// components
import Card from "@/components/Card";
import FormText from "@/components/FormText";

export default {
  name: "Analyse",
  components: {
    Card,
    FormText,
  },
  props: {
    cache: {
      type: [String, null],
      default: null,
    },
  },
  emits: ["startLoading", "endLoading", "clearCache"],
  setup(_props, ctxt) {
    const { cache } = toRefs(_props);
    const target = reactive({
      text: "",
      feedback: "",
    });
    const results = reactive({
      token: [],
    });

    watch(
      () => target.text,
      (newVal, oldVal) => {
        if (newVal === oldVal) return;
        if (newVal.length > 500) {
          target.feedback = "文章が長すぎます。";
        } else {
          target.feedback = "";
        }
      }
    );

    // 文字列クリア
    const clear = () => {
      results.token.splice(0);
      target.text = "";
    };

    const withLoading = async (cb) => {
      ctxt.emit("startLoading");
      try {
        return await cb();
      } catch (e) {
        console.err(e);
      } finally {
        ctxt.emit("endLoading");
      }
    };

    // 分析実行
    const exec = async () => {
      if (target.feedback.length > 0) return;

      await withLoading(async () => {
        const result = await analyse(target.text);
        results.token = result;
      });
    };

    // 保存処理
    const store = async () => {
      if (target.text.length === 0) {
        target.feedback = "1文字以上の文字列を入力してください。";
        return;
      }
      if (target.feedback.length > 0) return;

      await withLoading(async () => {
        const data = new URLSearchParams();
        data.append("sentence", target.text);
        data.append("words", JSON.stringify(results.token));
        await api.post("/sentences", data);
      });
    };

    // 親にcacheの値があったらおろしてもらう...
    onMounted(() => {
      if (cache.value) {
        target.text = cache.value;
        ctxt.emit("clearCache");
      }
    });

    return {
      ...toRefs(target),
      ...toRefs(results),
      exec,
      store,
      clear,
    };
  },
};
</script>