<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 text-gray-600 mt-2">
                    <div class="bg-white card p-4 mb-4">
                        <div class="survey-card">
                            <h2 class="text-xl font-semibold">{{ survey.title }}</h2>
                            <p>{{ survey.description }}</p>
                        </div>
                    </div>

                    <!-- Check if the survey has been submitted -->
                    <div v-if="submitted && !view_responses">
                        <!-- Display a message and a link to view answers -->
                        <div class="bg-green-100 rounded-xl border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">You have already participated in this survey. Thank you! <a href="#" @click="viewAnswers" class="underline">View My Answers</a></span>
                        </div>
                    </div>

                    <!-- If the survey hasn't been submitted, show the questions -->
                    <div v-else class="p-4 bg-white rounded-xl">
                        <div v-for="(question, index) in survey.questions" :key="question.id">
                            <question-answering :responses="$page.props.responses" :session_id="$page.props.participantId" class="w-full" :questionType="question.question_type" :index="index" :question="question" @answer="handleAnswer" />
                        </div>
                        <button v-if="!submitted" :disabled="submitting" @click="submitSurvey" class="btn">
                            <span v-if="!submitting">Submit Survey</span>
                            <span v-else class="loading loading-bars loading-md"></span>
                        </button>
                    </div>

                    <!-- Success message for submission -->
                    <div :class="'toast toast-start toast-bottom hidden'">
                        <div class="alert alert-success animate__animated animate__slideInLeft">
                            <span>Responses submitted successfully.</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import QuestionAnswering from "@/Components/Custom/QuestionAnswering.vue";
import InputType from "@/Components/Custom/InputType.vue";
import 'animate.css';
import swal from "sweetalert";
export default {
    components: {
        QuestionAnswering, InputType
    },
    data() {
        return {
            submitted: false,
            submitting: false,
            view_responses: false,
            survey: {}, // Survey data received from the API
            participantAnswers: [], // To store participant answers
        };
    },
    methods: {
        handleAnswer(answerData) {
            // Handle answers from the QuestionAnswered component
            // Push the answer data to participantAnswers
            this.participantAnswers.push(answerData);
        },
        async submitSurvey() {
            // Make an HTTP POST request to submit the survey
            try {
                const response = await axios.post(route('survey.submit-responses'), {
                    session_id: this.$page.props.participantId, // Pass the session_id to identify the responses
                    survey_id: this.$page.props.survey.survey_id,   // Pass the survey_id to identify the responses
                });

                if (response.status === 200 || response.status === 201) {
                    // Survey submitted successfully
                    this.submitted = true;
                    await swal('Done', 'Survey submitted successfully.', 'success');
                } else {
                    // Handle errors or display an error message
                    alert('Failed to submit survey.');
                }
            } catch (error) {
                // Handle any errors that occur during the request
                console.error('Error submitting survey:', error);
            }
        },
        viewAnswers() {
            this.view_responses = true;
        },
    },
    mounted() {
        // Fetch survey details from your API based on the survey ID or slug
        // Example: axios.get(`/api/surveys/${surveyId}`)
        // Populate the survey data
        // This assumes that you receive survey data with questions
        // Example:
        this.survey = this.$page.props.survey;
        let responses = this.$page.props.responses;
        //alert(this.$page.props.survey.survey_id)
        if(responses.length && responses[0].is_submitted){
            this.submitted = true;
        }
        /*this.survey = {
            title: "Sample Survey",
            description: "Please answer the following questions.",
            questions: [
                {
                    id: 1,
                    text: "What is your age?",
                    type: "number",
                },
                {
                    id: 2,
                    text: "Do you like this product?",
                    type: "multiple-choice",
                    options: ["Yes", "No"],
                },
                // Add more questions here
            ],
        };*/
    },
};
</script>
