<template>
    <div class="form-group">
        <label :for="name">{{name}}</label>
        <input :type="type" v-model="model"/>
        <small class="text-red-500">{{ message }}</small>
    </div>
</template>

<script setup>
const model = defineModel({
    type: null,
    required: true
})

defineProps({
   name: {
       type: String,
       required: true,
   },
   type: {
       type: String,
       default: "text"
   },
   message: String,
})
</script>

<style scoped>
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
</style>
