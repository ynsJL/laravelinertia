<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div class="mb-5">
        <p class="text-sm text-amber-300">
            {{ message }}
        </p>
    </div>
    <!-- <div v-show="success" class="flex-center-center">
        <div class="flash-message flash-message--success mb-0 mt-5">
        <p class="flash-message__text">{{ $success }}</p>
        <i class="fa-solid fa-xmark flash-message__icon js-hide-flash"></i>
        </div>
    </div> -->
</template>
