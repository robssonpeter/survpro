<template>
    <QuillEditor @textChange="changing" :content="text" content-type="html" v-model:content="content" theme="snow" />
</template>

<script>
    import { QuillEditor } from '@vueup/vue-quill'
    import '@vueup/vue-quill/dist/vue-quill.snow.css';

    export default {
        name: "TextEditor",
        components: {
            QuillEditor,
        },
        props: {
           text: {
               type: String
           }
        },
        data() {
          return {
              content: ''
          }
        },
        methods: {
            changing(){
                /*let converter = new QuillDeltaToHtmlConverter(this.content.ops, {});
                let html = converter.convert();*/
                this.$emit('change', this.content);
                //console.log(this.content)
            }
        }
    }
</script>

<style scoped>

</style>
