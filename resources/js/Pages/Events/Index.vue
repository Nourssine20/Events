<script setup>
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import moment from "moment";
import AppLayout from "@/Layouts/AppLayout";
import AddEditDialog from "./Partials/AddEditDialog";
import Button from "@/Components/Common/Button";
import Dialog from "@/Components/Common/DialogModal";
import Table from "@/Components/Common/Table";

const props = defineProps({
  events: { type: Array, default: () => [] }
});

const filterTitle = ref("");
const filterStartDate = ref("");

const itemToEdit = ref(null);
const itemToDelete = ref(null);

const filteredEvents = computed(() =>
  props.events.filter(event => {
    const matchesTitle =
      !filterTitle.value ||
      (event.title ?? "")
        .toLowerCase()
        .includes(filterTitle.value.toLowerCase());

    const matchesDate =
      !filterStartDate.value ||
      moment(event.starts_at).format("YYYY-MM-DD") === filterStartDate.value;

    return matchesTitle && matchesDate;
  })
);

function onDelete() {
  if (!itemToDelete.value) return;
  Inertia.delete(route("events.destroy", itemToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => (itemToDelete.value = null)
  });
}
</script>

<template>
  <AppLayout title="Events">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Events</h2>
    </template>
    <div class="card">
      <div class="flex gap-3 mb-4">
        <input
          v-model="filterTitle"
          type="text"
          placeholder="Filtrer by title"
          class="border rounded px-2 py-1 w-1/3"
        />
        <input
          v-model="filterStartDate"
          type="date"
          class="border rounded px-2 py-1 w-1/3"
        />
      </div>

      <div class="mb-6 flex flex-row justify-between items-end">
        <AddEditDialog :item-to-edit="itemToEdit" @close="itemToEdit = null" />
      </div>
      <Dialog :show="itemToDelete !== null" @close="itemToDelete = null">
        <template #header>Deleting event</template>
        <p>Are you sure you want to delete this event?</p>
        <template #footer>
          <Button variant="secondary" class="mr-3" @click="itemToDelete = null">Cancel</Button>
          <Button variant="danger" @click="onDelete">Submit</Button>
        </template>
      </Dialog>

      <Table :data="filteredEvents" :headings="['Title', 'Start Date', 'End Date', 'Actions']">
        <template #row="{ item }">
          <td>{{ item.title }}</td>
          <td>{{ moment(item.starts_at).format("HH:mm DD/MM/YYYY") }}</td>
          <td>{{ item.ends_at ? moment(item.ends_at).format("HH:mm DD/MM/YYYY") : "-" }}</td>
          <td>
            <span class="px-2 text-gray-700 hover:text-blue-500 cursor-pointer transition"
                  @click="itemToEdit = item">
              <vue-feather type="edit" size="1.3rem" />
            </span>
            <span class="px-2 text-gray-700 hover:text-red-500 cursor-pointer transition"
                  @click="itemToDelete = item">
              <vue-feather type="trash" size="1.3rem" />
            </span>
          </td>
        </template>
      </Table>
    </div>
  </AppLayout>
</template>
