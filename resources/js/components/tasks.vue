<template>
  <v-app>
    <div class="content-wrapper">
      <div v-if="is('Super Admin') || can('tasks')">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-6">
                <h1 class="m-0 text-dark">{{ $t("message.TASKS") }}</h1>
              </div>
              <!-- /.col -->
              <div class="col-6">
                <button
                  class="btn btn-success float-right"
                  @click="addTask"
                  v-if="is('Super Admin') || can('create_task')"
                >
                  {{ $t("message.CREATE_TASK") }}
                  <i class="fas fa-plus fa-fw"></i>
                </button>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <div class="card-tools">
                      <form @submit.prevent>
                        <v-text-field
                          class="m-0 p-0"
                          v-model="search"
                          append-icon="mdi-magnify"
                          label="Search"
                          single-line
                          hide-details
                        ></v-text-field>
                      </form>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body table-responsive p-0">
                    <v-data-table
                      :headers="headers"
                      :items="tasks.data ? tasks.data : tasks"
                      :server-items-length="tasks.total"
                      class="elevation-1"
                      :loading="loading"
                      :options.sync="options"
                      :search="search"
                      :footer-props="{
                        showFirstLastPage: true,
                        'items-per-page-options': [10, 50, 100, 500, -1],
                      }"
                    >


                    <template #item.is_completed="{ item }">
                      <span>{{ (item.is_completed == true || item.is_completed == 1) ? 'Yes' : 'No' }}</span>
                    </template>
                    
                      <template v-slot:item.actions="{ item }">

                        <v-icon
                          color="green"
                          class="edit-icon mr-2"
                          small
                          @click="viewTask(item)"
                        >
                          mdi-eye
                        </v-icon>

                        <v-icon
                          color="blue"
                          class="edit-icon mr-2"
                          small
                          @click="editTask(item)"
                          v-if="is('Super Admin') || can('edit_task')"
                        >
                          mdi-pencil
                        </v-icon>

                        <v-icon
                          color="red"
                          class="delete-icon"
                          small
                          @click="deleteTask(item.id)"
                          v-if="is('Super Admin') || can('delete_task')"
                        >
                          mdi-delete
                        </v-icon>
                      </template>
                    </v-data-table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
            </div>
          </div>
        </div>
        <!-- /.content -->
        <addEditTaskModal></addEditTaskModal>
        <viewTaskModal :taskData="taskInfo"></viewTaskModal>
      </div>
      <div class="unathorized-text" v-else>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 unauthorized">
              <v-alert color="red lighten-2" dark>
                {{ $t("message.UNAUTHORIZED") }}
              </v-alert>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-app>
</template>

<script>
import addEditTaskModal from "./modals/addEditTaskModal.vue";
import viewTaskModal from "./modals/viewTaskModal.vue";
export default {
  data() {
    return {
      form: new form(),
      tasks: [],
      curpage: 1,
      search: "",
      itemsPerPage: 10,
      loading: true,
      options: {},
      sortBy: "",
      sortDesc: "",
      taskInfo: {},
      task_id: "",
      headers: [
        { text: this.$t("message.NAME"), value: "name" },
        { text: this.$t("message.USER"), value: "user.name" },
        { text: "Completed", value: "is_completed" },
        {
          text: this.$t("message.ACTIONS"),
          value: "actions",
          sortable: false,
        },
      ],
    };
  },
  components: {
    addEditTaskModal,
    viewTaskModal,
  },
  watch: {
    //DataTable watcher!
    options: {
      handler() {
        this.getResults(
          this.options.page ? this.options.page : null,
          this.options.itemsPerPage ? this.options.itemsPerPage : null
        );
      },
      deep: true,
    },
    search() {
      this.getResults();
    },
  },
  methods: {
    getResults(page = 1, rows = 10, sortBy = null, sortDesc = null) {
      if (this.is("Super Admin") || this.can("tasks")) {
        this.$Progress.start();
        this.loading = true;
        this.curpage = page;
        if (this.options.sortBy && this.options.sortBy.length > 0) {
          this.sortBy = this.options.sortBy[0];
        } else {
          this.sortBy = "";
        }
        if (this.options.sortDesc && this.options.sortDesc.length > 0) {
          this.sortDesc = this.options.sortDesc[0];
        } else {
          this.sortDesc = "";
        }
        axios
          .get(
            "api/tasks?page=" +
              page +
              "&search=" +
              this.search +
              "&numRows=" +
              rows +
              "&sortBy=" +
              this.sortBy +
              "&sortDesc=" +
              this.sortDesc
          )
          .then((response) => {
            this.tasks = response.data;
            this.$Progress.finish();
            this.loading = false;
          })
          .catch(() => {
            this.$Progress.fail();
            this.loading = false;
            toast.fire({
              icon: "error",
              title: this.$t("message.SOMETHING_WENT_WRONG"),
            });
          });
      } else {
        toast.fire({
          icon: "error",
          title: this.$t("message.UNAUTHORIZED"),
        });
        this.$Progress.fail();
        this.loading = false;
      }
    },
    deleteTask(id) {
      if (this.is("Super Admin") || this.can("delete_task")) {
        swal
          .fire({
            title: this.$t("message.CONFIRM"),
            text: this.$t("message.DELETE_MESSAGE_REVERT"),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: this.$t("message.DELETE_BUTTON_TEXT"),
          })
          .then((result) => {
            this.$Progress.start();
            if (result.value) {
              // Send request to the server
              this.form
                .delete("api/tasks/" + id)
                .then(() => {
                  this.tasks.total -= 1;
                  Fire.$emit("reloadTasks");
                  this.$Progress.finish();
                  swal.fire(
                    this.$t("message.DELETED"),
                    this.$t("message.DELETE_MESSAGE_SUCCESS"),
                    "success"
                  );
                })
                .catch(() => {
                  this.$Progress.fail();
                  swal.fire(
                    this.$t("message.FAILED"),
                    this.$t("message.DELETE_MESSAGE_ERROR"),
                    "warning"
                  );
                });
            }
          });
      } else {
        toast.fire({
          icon: "error",
          title: this.$t("message.UNAUTHORIZED"),
        });
      }
    },
    addTask() {
      if (this.is("Super Admin") || this.can("create_task")) {
        $("#addEditTaskModal").modal("show");
      } else {
        toast.fire({
          icon: "error",
          title: this.$t("message.UNAUTHORIZED"),
        });
      }
    },
    editTask(task) {
      if (this.is("Super Admin") || this.can("edit_task")) {
        this.taskInfo = task;
        $("#addEditTaskModal").modal("show", task);
      } else {
        toast.fire({
          icon: "error",
          title: this.$t("message.UNAUTHORIZED"),
        });
      }
    },
    viewTask(task) {
      this.taskInfo = task;
      $("#viewTaskModal").modal("show", task);
    },

    // Upload File
    uploadFile(id)
    {
      if (this.is("Super Admin") || this.can("edit_task")) {
        this.task_id = id;
        $("#uploadFileModal").modal("show");
      } else {
        toast.fire({
          icon: "error",
          title: this.$t("message.UNAUTHORIZED"),
        });
      }
    },

  },
  created() {
    Fire.$on("reloadTasks", () => {
      this.getResults(this.curpage);
    });
  },
};
</script>