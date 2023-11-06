<template>
    <d-modal v-model="showModal" title="Send Survey">
        <!-- Modal content -->
        <div class="p-6">
            <div>
                <label for="email">Recipient's Email:</label>
                <input v-model="recipientEmail" type="email" id="email" class="input" />
            </div>

            <div class="mt-4">
                <button @click="sendSurvey">Send</button>
            </div>
        </div>
    </d-modal>
</template>

<script>
export default {
    props: {
        surveyLink: String,
    },
    data() {
        return {
            showModal: true, // Initially show the modal
            recipientEmail: "",
        };
    },
    methods: {
        sendSurvey() {
            // Handle sending the survey to the recipient's email
            // You can use an API request here to send the survey link.
            // After sending, you can close the modal.

            // For example, you can use Axios:
            // axios.post('/send-survey', { email: this.recipientEmail, surveyLink: this.surveyLink })
            //   .then(response => {
            //     // Handle success or error
            //   })
            //   .catch(error => {
            //     // Handle error
            //   });

            // Close the modal after sending
            this.showModal = false;

            // Reset recipientEmail for the next use
            this.recipientEmail = "";
        },
    },
};
</script>
