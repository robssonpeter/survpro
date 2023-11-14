<template>
    <div class="card bg-white shadow-md flex-grow mb-4">
        <div class="card-body">
            <h2 class="card-title">3 Responses</h2>
        </div>
        <section class="flex">
            <div class="flex-grow"></div>
            <div class="tabs text-gray-800">
                <a :class="`tab hover:text-primary tab-bordered ${current_response_tab === 'summary'?activeTab:''}`" @click="currentTab('summary', 'current_response_tab')">Summary</a>
                <a :class="`tab hover:text-primary tab-bordered ${current_response_tab === 'question'?activeTab:''}`" @click="currentTab('question', 'current_response_tab')">Question</a>
                <a :class="`tab hover:text-primary tab-bordered ${current_response_tab === 'individual'?activeTab:''}`" @click="currentTab('individual', 'current_response_tab')">Individual</a>
            </div>
            <div class="flex-grow"></div>
        </section>
    </div>
    <QuestionResponseSummary :index="index" v-for="(question, index) in surveyStore.questions" />
</template>

<script>
import QuestionResponseSummary from '@/Components/Custom/QuestionResponseSummary.vue'
import { useSurveyStore } from "@/Stores/SurveyStore.js";
import { ref, computed } from 'vue'
import axios from 'axios'
import io from 'socket.io-client'

export default {
    name: "SurveyAdminResponses",
    components: {
        QuestionResponseSummary
    },
    computed: {
        activeTab() {
            return 'tab-active text-primary border-b-primary font-semibold'
        }
    },
    setup() {
        const surveyStore = useSurveyStore()
        const current_response_tab = ref('summary')
        //const response_socket = io(route('survey.responses'))

        const currentTab = (tab, type = 'current_tab') => {
            current_response_tab.value = tab;
        };

        const surveyResponses = async () => {
            try {
                // Use the axios instance directly
                const response = await axios.get('your_api_endpoint');
                console.log(response.data);
                // Handle the response as needed
            } catch (error) {
                console.error('Error fetching survey responses:', error);
            }
        }

        const responseCount = computed(() => {
            let classified = surveyStore.responses.map()
        });

        return {
            surveyStore,
            current_response_tab,
            currentTab,
            surveyResponses
        }
    }
}
</script>

<style scoped>

</style>
