<template>
<!--    <span>{{ response.question_id }}</span>-->
    <section v-if="response && Object.keys(response).length > 2">
    <span><strong>{{ question.question_text }}</strong></span>
    <section v-if="question.question_type === 'checkbox' && question.options">
        <section v-for="option in question.options">
            <input type="checkbox" :checked="selectedOptions.indexOf(option.text) > -1" disabled class="checkbox" /> <span>{{ option.text }}</span>
        </section>
    </section>
    <div v-else>
        <span style="text-decoration: underline dotted">{{ response.response_text }}</span>
    </div>
    </section>
</template>

<script>
import {useSurveyStore} from "@/Stores/SurveyStore.js";
import {computed} from 'vue';
export default {
    name: "RenderQuestionResponse",
    props: ['response'],
    setup(props){
        const surveyStore = useSurveyStore()

        const question = computed(() => {
            return surveyStore.questions.find(el => el.question_id === props.response.question_id);
        });

        const selectedOptions = computed(() => {
            return JSON.parse(props.response.response_text);
        })

        return {
            surveyStore,
            question,
            selectedOptions,
        }
    }
}
</script>

<style scoped>

</style>
