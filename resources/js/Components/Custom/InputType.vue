<template>
    <div>
        <div v-if="questionType === 'drop-down'">
<!--            <span>The input value is {{ inputValue.length }}</span>-->
            <select
                v-model="inputValue"
                :disabled="disabled"
                @change="entered"
                class="select select-ghost w-full max-w-xs">
                <option :value="null" disabled selected>Select Answer</option>
                <option v-for="option in options">{{ option.text }}</option>
            </select>
        </div>
        <div v-if="questionType === 'paragraph'">
            <div class="w-full flex">
                <textarea
                v-model="inputValue"
                :disabled="disabled"
                class="form-control textarea textarea-ghost textarea-md flex-grow"
                rows="4"
                placeholder="Type your answer here"
                @change="entered"
            ></textarea></div>

        </div>
        <div v-else-if="questionType === 'number'">
            <input
                v-model="inputValue"
                type="number"
                :disabled="disabled"
                class="input input-ghost input-md"
                @change="entered"
                placeholder="Answer"
            />
        </div>
        <div v-else-if="questionType === 'date'">
            <input
                v-model="inputValue"
                :disabled="disabled"
                type="date"
                placeholder="Answer"

                class="input input-ghost input-md"
            />
        </div>
        <div v-else-if="questionType === 'time'">
            <input
                v-model="inputValue"
                :disabled="disabled"
                type="time"
                placeholder="Answer"
                class="input input-ghost input-md"
            />
        </div>
        <div v-else-if="questionType === 'short-answer'">
            <input
                v-model="inputValue"
                :disabled="disabled"
                type="text"
                placeholder="Answer"
                class="input input-ghost input-md"
            />
        </div>
        <div v-else-if="questionType === 'file-upload'">
            <input
                type="file"
                :disabled="disabled"
                class="input input-ghost input-md"
                @change="handleFileChange"
            />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        questionType: String, // Pass the question type as a prop
        value: String, // Value for v-model
        options: Object,
        disabled: Boolean,
    },
    mounted(){
        //alert('hello there')
    },
    data() {
        return {
            inputValue: this.value, // Initialize inputValue with the provided value
        };
    },
    watch: {
        value(newValue) {
            // Update inputValue when the value prop changes (e.g., when using v-model)
            this.inputValue = newValue;
            this.$emit('input', newValue);
        },
        inputValue(newValue) {
            // Emit an input event when inputValue changes
            this.$emit('input', newValue);
        },
    },
    methods: {
        entered(value){
            console.log(value.target.value)
            this.$emit('input', value.target.value);
        },
        handleFileChange(event) {
            // Handle file input change and emit the selected file
            this.$emit('input', event.target.files[0]);
        },
    },
};
</script>

<style scoped>
/* Add custom styling for the inputs if needed */
</style>
