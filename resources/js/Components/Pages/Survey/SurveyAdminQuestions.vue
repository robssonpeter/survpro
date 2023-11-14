<template>
    <div class="flex space-x-8 mb-4">
        <!-- Survey Header -->
        <div class="card bg-white shadow-md flex-grow">
            <div class="card-body">
                <div class="flex">
                    <h2 class="card-title flex-grow" v-if="!editing_survey.title" @click="editing_survey.title = true">
                        {{ surveyStore.newSurvey.title ? surveyStore.newSurvey.title : 'Survey Title - (Click to Add)' }}
                    </h2>
                    <input
                        v-if="editing_survey.title"
                        v-model="surveyStore.newSurvey.title"
                        @change="storeSurvey('title')"
                        type="text"
                        placeholder="Type here and press enter"
                        class="input input-ghost flex-grow w-full max-w-xs focus:bg-gray-50"
                    />
                    <div class="flex-grow" v-if="editing_survey.title"></div>
                    <select
                        @change="storeSurvey('all')"
                        v-model="surveyStore.newSurvey.status"
                        class="select select-ghost focus:bg-gray-100 select-bordered max-w-xs"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div v-if="!editing_survey.description" @click="editing_survey.description = true">
                    {{ surveyStore.newSurvey.description ? surveyStore.newSurvey.description : 'Survey description (Click to add)' }}
                </div>
                <textarea
                    placeholder="Bio"
                    v-if="editing_survey.description"
                    v-model="surveyStore.newSurvey.description"
                    @change="storeSurvey('description')"
                    class="textarea textarea-ghost textarea-bordered textarea-xs w-full max-w-xs focus:bg-gray-50"
                ></textarea>
            </div>
        </div>
        <!-- Button for adding questions -->
        <div
            :class="`bg-white rounded-xl align-self-start p-2 self-center ${questions.length > 0 ? 'invisible' : ''}`"
        >
            <!-- The plus sign -->
            <span class="tooltip" data-tip="Add a question" @click="addAQuestion(0)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </span>
        </div>
    </div>
    <div class="flex space-x-8 mb-2" v-for="(question, index) in surveyStore.questions">
        <RenderQuestion
            class="flex-grow"
            :survey_id="$page.props.survey ? $page.props.survey.survey_id : null"
            @deleted="removeQuestion"
            @updating="focus_question"
            :focused="updating === index"
            :qtypes="$page.props.question_types"
            :data="question"
            :reference="index"
        ></RenderQuestion>
        <div :class="`bg-white p-2 flex flex-col mb-4 rounded-xl ${updating === index ? '' : 'invisible'}`">
            <!-- The plus sign -->
            <span class="tooltip" data-tip="Add a question above this" @click="addClicked(index, 'before')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </span>
            <div class="flex-grow"></div>
            <span class="tooltip" data-tip="Add a question below this" @click="addClicked(index, 'after')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </span>
        </div>
    </div>
</template>

<script>
import RenderQuestion from "@/Components/Custom/RenderQuestion.vue";
import { ref, onMounted, defineProps, defineEmits } from "vue";
import { useSurveyStore } from "@/Stores/SurveyStore.js";
import axios from "axios";

export default {
    name: "SurveyAdminQuestions",
    props: ['survey', 'questions'],
    components: {
        RenderQuestion
    },
    setup(props) {
        const surveyStore = useSurveyStore();
        const editing_survey = ref({
            title: false,
            description: false
        });
        const updating = ref(0);
        const questions = ref([]);
        const new_question = ref({
            question_text: "",
            question_type: "",
            options: [],
            is_required: false
        });
        const newSurvey = ref({
            title: "",
            description: "",
            due_date: "",
            status: "draft",
            created_at: "",
            updated_at: ""
        });

        const emit = defineEmits(['questionRemoved']);

        onMounted(() => {

            if (props.survey) {
                surveyStore.newSurvey = props.survey
                //newSurvey.value = props.survey;
                surveyStore.questions = props.questions;
            }
            if (surveyStore.questions.length > 0) {
                updating.value = surveyStore.questions.length - 1;
            }
            fetchSurveyResponses()
        });

        const addAQuestion = (position) => {
            //this.updating = (this.questions.length - 1)
            //alert('question is being added')
            const newQuestion = {
                question_text: '',
                question_type: 'short-answer',
                options: [],
                is_required: false,
            }

            if(position === 'before' || !surveyStore.questions.length){

                if(updating.value > 0){
                    surveyStore.questions.splice(updating.value , 0, newQuestion);
                    updating.value = updating.value - 1;
                }else{
                    surveyStore.questions.splice(updating.value , 0, newQuestion);
                }

                console.log('the if has run');
            }else{
                updating.value = updating.value + 1;
                surveyStore.questions.splice(updating.value, 0, newQuestion);

                console.log("the else has run here")
            }
        }

        const removeQuestion = (reference) => {
            surveyStore.questions.splice(reference, 1);
            if (reference > 0) {
                updating.value = reference - 1;
            }
           //emit("questionRemoved", reference);
        };

        const focus_question = (e) => {
            updating.value = e;
        };

        const fetchSurveyResponses = async () => {
            try {
                const response = await axios.get(route('surveys.responses', surveyStore.newSurvey.survey_id));
                surveyStore.responses = response.data; // Update the responses array with new data
                console.log('responses successfully pulled as shown below')
            } catch (error) {
                console.error()
                console.error('Error fetching survey responses:', error);
            }
        };

        const storeSurvey = async (variable = "all") => {
            if (variable !== "all") {
                editing_survey.value[variable] = false;
            }
            try {
                const response = await axios.post(route("surveys.store"), surveyStore.newSurvey);
                console.log(response)
                if (response.status === 200 || response.status === 201) {
                    console.log("Survey data has been stored:", response.data);
                    if (!props.survey) {
                        emit("surveyModify", response.data.stored.hashslug);
                    }
                } else {
                    console.log("Data could not be saved");
                }
            } catch (error) {
                console.error("Error while storing survey data:", error);
            }
        };

        const addClicked = (index, position) => {
            addAQuestion(position);
        }

        return {
            editing_survey,
            updating,
            questions,
            new_question,
            surveyStore,
            newSurvey,
            removeQuestion,
            focus_question,
            storeSurvey,
            addClicked,
            addAQuestion,
            fetchSurveyResponses
        };
    }
};
</script>

<style scoped>
</style>
