<template>
    <div class="card w-full bg-white shadow-md">
        <div class="card-body">
            <section class="flex space-x-6 pb-4">
                <select v-model="currentIndex" class="select select-ghost focus:bg-gray-200 select-bordered w-full max-w-xs">
                    <option disabled selected>Question</option>
                    <option :value="index" class="text-gray-600" v-for="(question, index) in surveyStore.questions">{{ question.question_text }}</option>
                </select>
                <div class="self-center flex space-x-5">
                    <!--  less than sign  -->
                    <svg @click="downIndex" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span>{{ currentIndex+1 }} of {{ surveyStore.questions.length }}</span>
                    <!--  greater than sign  -->
                    <svg @click="upIndex" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>

                </div>
            </section>
            <div class="pt-4 border-t">
                <span class="font-bold">{{ surveyStore.questions[currentIndex].question_text }}</span>
            </div>
        </div>
    </div>
    <div class="card w-full bg-white shadow-xl mt-4" v-for="response in surveyStore.classifiedResponses(currentIndex)">

        <div class="card-body">
            <section class="bg-gray-100 p-2" style="max-height: 400px; overflow-y: auto">
                <div v-if="surveyStore.questions[currentIndex].question_type === 'checkbox'">
                    <div class="flex space-x-2 mb-1" v-for="resp in renderCheckboxAnswer(response.response_text)">
                        <input type="checkbox" checked="checked" disabled class="checkbox" />
                        <span class="text-gray-600">{{ resp }}</span>
                    </div>
                </div>
                <span v-else>{{response.response_text}}</span>
            </section>
<!--            <div class="divider"></div>-->
            <section class="border-t pt-4">
            <em>{{ response.count }} {{ response.count === 1?'response':'responses' }}</em>
            </section>
        </div>

    </div>
</template>

<script>
import { useSurveyStore } from "@/Stores/SurveyStore.js";
import {computed, ref} from 'vue'

export default {
    name: "QuestionResponsesView",
    setup (props){
        const surveyStore = useSurveyStore();

        const currentIndex = ref(0)

        const classifiedResponses = computed(() => {
            surveyStore.classifiedResponses(currentIndex.value)
        });

        const question = surveyStore.questions[currentIndex];

        const renderCheckboxAnswer = computed(() => {
            return (answer) => {
                return JSON.parse(answer);
            }
        })

        const upIndex = () => {
            if(Number(currentIndex.value) < surveyStore.questions.length - 1){
                currentIndex.value = currentIndex.value + 1;
            }
        }

        const downIndex = () => {
            if(currentIndex.value > 0){
                currentIndex.value = currentIndex.value - 1;
            }
        }

        return {
            surveyStore,
            currentIndex,
            classifiedResponses,
            question,
            renderCheckboxAnswer,
            upIndex,
            downIndex
        }
    }
}
</script>

<style scoped>

</style>
