<template>
    <div class="mb-4">
        <div class="question">{{ index+1 }}. {{ question.question_text }}</div>
        <input-type v-model="answer" :questionType="question.question_type" @input="handleInput"></input-type>
        <div v-if="question.question_type === 'short-answer'">
            <input
                v-model="answer"
                :type="inputType"
                @input="handleInput"
            />
        </div>
        <div v-else-if="question.question_type === 'paragraph'">
            <span>hi there</span>

            <!--      <textarea
                      v-model="answer"
                      :type="inputType"
                      @input="handleInput"
                  ></textarea>-->
        </div>
        <div
            class="px-5"
            v-else-if="question.question_type === 'multiple-choice' || question.question_type === 'dropdown' || question.question_type === 'checkbox'" >
            <div v-for="(option, index) in JSON.parse(question.options)" :key="index">
                <label :for="'option-' + index">
                    <input
                        :type="question.question_type === 'multiple-choice' ? 'radio' : 'checkbox'"
                        :name="'question-' + question.question_id"
                        :id="'option-' + index"
                        :value="option.text"
                        v-model="selectedOptions"
                        @change="handleInput"
                    />
                    {{ option.text }}
                </label>
            </div>
        </div>
        <!-- Add other question types as needed -->
    </div>
</template>

<script>
import InputType from "@/Components/Custom/InputType.vue";
export default {
    props: {
        question: Object,
        index: Number,
    },
    components: [
        InputType
    ],
    data() {
        return {
            answer: '',
            selectedOptions: [],
        };
    },
    computed: {
        inputType() {
            // Determine the input type based on the question type
            if (this.question.question_type === 'number') return 'number';
            if (this.question.question_type === 'date') return 'date';
            return 'text'; // Default to text input
        },
    },
    methods: {
        handleInput() {
            // Emit the answer to the parent component
            this.$emit('answer', {
                questionId: this.question.id,
                answer: this.answer,
                selectedOptions: this.selectedOptions,
            });
        },
    },
};
</script>
