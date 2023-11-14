<template>
    <div class="mb-4">
        <div class="question font-semibold">{{ index + 1 }}. {{ question.question_text }}</div>
        <InputType :disabled="submitted" :options="questionOptions" :value="answer" :questionType="question.question_type" @input="handleInput" />
        <div
            class="px-5"
            v-if="question.question_type === 'multiple-choice' || question.question_type === 'dropdown' || question.question_type === 'checkbox'" >
            <div v-for="(option, index) in JSON.parse(question.options)" :key="index">
                <label :for="'option-' + index">
                    <input
                        :type="question.question_type === 'multiple-choice' ? 'radio' : 'checkbox'"
                        :name="'question-' + question.question_id"
                        :id="'option-' + index"
                        :value="option.text"
                        :disabled="submitted"
                        v-model="selectedOptions"
                        @change="handleSelectedInput"
                    />
                    {{ option.text }}
                </label>
            </div>
        </div>
    </div>
</template>

<script>
import InputType from "@/Components/Custom/InputType.vue";
import axios from "axios";
export default {
    props: {
        question: Object,
        index: Number,
        session_id: String, // used as an identifier of the participant
        responses: Object,
        disabled: Boolean,
    },
    components: {
        InputType,
    },
    computed: {
        questionOptions(){
            if(typeof(this.question.options) === 'string'){
                return JSON.parse(this.question.options)
            }
            return this.question.options
        }
    },
    mounted(){
      //alert(this.question.survey_id)
        let response = this.responses.find(element => element.question_id === this.question.question_id);
        if(response){
            //alert(response.response_text)
            if(response.is_submitted){
                this.submitted = true;
            }
            this.answer = response.response_text;
            //alert(this.question.question_type)
            let option_questions = [
                'checkbox', 'multiple-choice'
            ]
            if(option_questions.indexOf(this.question.question_type) >= 0){
                //alert('there is an answer for options')
                this.selectedOptions = JSON.parse(response.response_text);
            }
        }
    },
    data() {
        return {
            submitted: false,
            answer: null, // Use null for initial value
            selectedOptions: [],
        };
    },
    methods: {
        handleSelectedInput(){
            this.handleInput(JSON.stringify(this.selectedOptions))
        },
        async handleInput(value) {
            const data = {
                questionId: this.question.question_id,
                sessionId: this.session_id,
                surveyId: this.question.survey_id,
                answer: value,
            };

            if(typeof(value) === "string"){
                try {
                    const response = await axios.post(route('response.store'), data);
                    if (response.status === 200 || response.status === 201) {
                        console.log('Response stored successfully:', response.data.message);
                    } else {
                        console.error('Failed to store response:', response.data);
                    }
                } catch (error) {
                    console.error('Error storing response:', error);
                }

                // Emit the answer to the parent component if needed
                this.$emit('answer', data);
            }
        },
    },
};
</script>
