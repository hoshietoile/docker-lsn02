// kuromoji
import kuromoji from "kuromoji";
import axios from 'axios'
const _builder = kuromoji.builder({ dicPath: "/dict" });
axios.defaults.baseURL = 'http://localhost:80'
axios.defaults.headers.post["Content-Type"] = "application/json;";

export const api = axios

export const analyse = async (value) => {
  return await _execKm(value)
}

// kuromojiのpromise化
const _execKm = (value) => {
  return new Promise((resolve, reject) => {
    _builder.build((err, tokenizer) => {
      if (err) {
        reject(err);
      } else {
        const result = tokenizer.tokenize(value);
        resolve(result);
      }
    });
  })
}
