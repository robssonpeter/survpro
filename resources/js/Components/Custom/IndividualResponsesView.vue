<template>
    <div class="card w-full bg-white shadow-md">
        <div class="card-body">
            <section class="flex space-x-6 pb-4">
                <div class="self-center flex space-x-5">
                    <!--  less than sign  -->
                    <svg @click="downIndex" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span>{{ currentIndex+1 }} of {{ surveyStore.response_count }}</span>
                    <!--  greater than sign  -->
                    <svg @click="upIndex" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>

                </div>
            </section>
        </div>
    </div>

<!--    <span>{{JSON.stringify(responses)}}</span>-->
    <div class="card w-full bg-white shadow-xl mt-4" v-for="response in responses[currentParticipant]">
        <div class="card-body">
<!--            <span>{{ participants }}</span>-->
<!--            <span>{{ surveyStore.response_count }}</span>
            <span>{{ responses }}</span>-->
            <RenderQuestionResponse :response="response" />
        </div>
    </div>
</template>

<script>
import {useSurveyStore} from "@/Stores/SurveyStore.js";
import {ref, computed, onMounted} from 'vue';
import RenderQuestionResponse from "@/Components/Custom/RenderQuestionResponse.vue";
export default {
    name: "IndividualResponsesView",
    components: {
      RenderQuestionResponse
    },
    setup(props){
        const surveyStore = useSurveyStore();

        const currentIndex = ref('')

        const responses = computed(() => {
            return surveyStore.classifyIndividualResponses(surveyStore.responses)
        })

        const participants = computed(() => {
            return Object.keys(responses.value);
        })

        const currentParticipant = ref()

        const classifyResponsesBySession = (responses) => {
            const classifiedResponses = {};

            responses.forEach(response => {
                const sessionId = response.session_id;

                // Check if the session_id is already a key in the classifiedResponses object
                if (!classifiedResponses[sessionId]) {
                    // If not, create a new array for that session_id
                    classifiedResponses[sessionId] = [];
                }

                // Add the response to the array for the corresponding session_id
                classifiedResponses[sessionId].push(response);
            });

            return classifiedResponses;
        }

        const upIndex = () => {
            let index = participants.value.findIndex((value) => currentParticipant.value === value)
            if(Number(index) < surveyStore.response_count - 1){
                currentParticipant.value = participants.value[index + 1];
                currentIndex.value = index + 1;
            }
        }

        const downIndex = () => {
            let index = participants.value.findIndex((value) => currentParticipant.value === value)
            if(index > 0){
                currentParticipant.value = participants.value[index - 1];
                currentIndex.value = index - 1;
            }
        }

        onMounted(() => {
            currentParticipant.value = participants.value[0]
        })

        return {
            surveyStore,
            currentIndex,
            responses,
            participants,
            currentParticipant,
            classifyResponsesBySession,
            upIndex,
            downIndex
        }
    }
}
</script>

<style scoped>

</style>
