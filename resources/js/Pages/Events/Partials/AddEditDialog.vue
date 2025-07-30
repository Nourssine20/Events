<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { useToast } from "vue-toastification";

const toast = useToast();
const emit = defineEmits(["close"]);
const props = defineProps({
    itemToEdit: Object,
});

const show = ref(false);

const form = useForm({
    title: "",
    starts_at: null,
    ends_at: null,
});

const isEditing = ref(false);

function openDialog(item = null) {
    isEditing.value = !!item;
    show.value = true;
    if (item) {
        form.title = item.title || "";
        form.starts_at = item.starts_at ? new Date(item.starts_at) : new Date();
        form.ends_at = item.ends_at ? new Date(item.ends_at) : new Date();
    } else {
        resetForm();
    }
}

function resetForm() {
    form.reset();
    const now = new Date();
    form.starts_at = now;
    form.ends_at = now;
}

function closeDialog() {
    show.value = false;
    resetForm();
    emit("close");
}

watch(
    () => props.itemToEdit,
    (item) => {
        if (item) openDialog(item);
        else closeDialog();
    },
);

function formatDateTime(date) {
    return date ? date.toISOString().slice(0, 16).replace("T", " ") : null;
}

function validateForm() {
    if (!form.title) return "The title is required.";
    if (!form.starts_at || !form.ends_at) return "The date is required.";
    if (form.ends_at <= form.starts_at)
        return "The end date must be after the start date.";
    return "";
}

function submitForm() {
    const error = validateForm();
    if (error) return toast.error(error);

    const params = { preserveScroll: true, onSuccess: closeDialog };
    form.transform((data) => ({
        ...data,
        starts_at: formatDateTime(data.starts_at),
        ends_at: formatDateTime(data.ends_at),
    }));

    if (isEditing.value && props.itemToEdit) {
        form.put(route("events.update", props.itemToEdit.id), params);
    } else {
        form.post(route("events.store"), params);
    }
}
</script>

<template>
    <div>
        <Button @click="openDialog()">
            <vue-feather type="plus" />
            <span class="ml-2">Add new</span>
        </Button>

        <Dialog :show="show" @close="closeDialog">
            <template #header>
                {{ isEditing ? "Edit event" : "Add new event" }}
            </template>

            <Input
                name="title"
                label="Title"
                v-model="form.title"
                class="mb-6"
            />

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Start Date</label
                >
                <Datepicker
                    v-model="form.starts_at"
                    :enable-time-picker="true"
                    :format="'yyyy-MM-dd HH:mm'"
                    class="w-full"
                />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >End Date</label
                >
                <Datepicker
                    v-model="form.ends_at"
                    :enable-time-picker="true"
                    :format="'yyyy-MM-dd HH:mm'"
                    class="w-full"
                />
            </div>

            <template #footer>
                <Button variant="secondary" class="mr-3" @click="closeDialog"
                    >Cancel</Button
                >
                <Button @click="submitForm">Submit</Button>
            </template>
        </Dialog>
    </div>
</template>
