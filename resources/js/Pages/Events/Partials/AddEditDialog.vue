<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import { useToast } from "vue-toastification";
const toast = useToast();

const emit = defineEmits(["close"]);

const props = defineProps({
    itemToEdit: {
        type: Object,
        default: null,
    },
});

const show = ref(false);
const editing = ref(false);

const form = useForm({
    title: "",
    starts_at: null,
    ends_at: null,
});

// Ouvre la modale en mode ajout avec valeurs par défaut
const onAddNew = () => {
    form.reset();
    show.value = true;
    editing.value = false;

    const now = new Date();
    form.starts_at = now.toISOString().slice(0, 16);
    form.ends_at = now.toISOString().slice(0, 16);
};

// Surveille les changements de itemToEdit pour gérer l’édition
watch(() => props.itemToEdit, (newItem) => {
    if (newItem) {
        editing.value = true;
        show.value = true;

        form.title = newItem.title || "";
        form.starts_at = newItem.starts_at
            ? new Date(newItem.starts_at).toISOString().slice(0, 16)
            : null;
        form.ends_at = newItem.ends_at
            ? new Date(newItem.ends_at).toISOString().slice(0, 16)
            : null;
    } else {
        // Reset si itemToEdit devient null (ex : fermeture modale)
        show.value = false;
        editing.value = false;
        form.reset();
    }
});

const onSubmit = () => {
    if (form.ends_at && form.ends_at < form.starts_at) {
    toast.error("The end date must be after the start date.");
    return;
  }
    const transform = (data) => ({
        ...data,
        starts_at: data.starts_at ? data.starts_at.replace("T", " ") : null,
        ends_at: data.ends_at ? data.ends_at.replace("T", " ") : null,
    });

    const requestParams = {
        preserveScroll: true,
        onSuccess: onClose,
    };

    if (editing.value && props.itemToEdit) {
        form.transform(transform).put(
            route("events.update", props.itemToEdit.id),
            requestParams,
        );
    } else {
        form.transform(transform).post(route("events.store"), requestParams);
    }
};

const onClose = () => {
    form.reset();
    show.value = false;
    emit("close");
};
</script>

<template>
  <div>
    <Button @click="onAddNew">
      <vue-feather type="plus" />
      <span class="ml-2">Add new</span>
    </Button>

    <Dialog :show="show" @close="onClose">
      <template #header>
        {{ editing ? "Edit event" : "Add new event" }}
      </template>

      <Input
        name="title"
        label="Title"
        v-model="form.title"
        class="mb-6"
      />

      <div class="mb-6">
        <label for="starts_at" class="block text-sm font-medium text-gray-700">Start Date</label>
        <input
          id="starts_at"
          name="starts_at"
          type="datetime-local"
          v-model="form.starts_at"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
          required
        />
      </div>

      <div class="mb-6">
        <label for="ends_at" class="block text-sm font-medium text-gray-700">End Date</label>
        <input
          id="ends_at"
          name="ends_at"
          type="datetime-local"
          v-model="form.ends_at"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
          required
        />
      </div>

      <template #footer>
        <Button variant="secondary" class="mr-3" @click="onClose">Cancel</Button>
        <Button @click="onSubmit">Submit</Button>
      </template>
    </Dialog>
  </div>
</template>

<style scoped>
</style>
