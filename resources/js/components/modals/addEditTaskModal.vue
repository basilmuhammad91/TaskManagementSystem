<template>
  <div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="addEditTaskModal"
      task="dialog"
      aria-labelledby="addEditTaskModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              class="modal-title"
              id="addEditTaskModalLabel"
              v-if="editMode === false"
            >
              {{ $t("message.CREATE_TASK") }}
            </h5>
            <h5 class="modal-title" id="addEditTaskModalLabel" v-else>
              {{ $t("message.EDIT_TASK") }}
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editMode ? editTask() : addTask()">
            <input type="hidden" name="_token" :value="csrf" />
            <div class="modal-body">
              
              <!-- Name -->
              <div class="form-group">
                <label
                  >{{ $t("message.NAME")
                  }}<span class="required-star">*</span></label
                >
                <input
                  v-model="form.name"
                  v-bind:placeholder="$t('message.NAME')"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                />
                <div
                  class="error-message"
                  v-if="form.errors.has('name')"
                  v-html="form.errors.get('name')"
                />
              </div>

              <div class="form-group">
                <label>Assign To<span class="required-star">*</span></label>
                <multiselect v-model="form.assign_to" :options="users" :multiple="true" :reduce="users => user.id" return="users => user.id" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Assign To" label="name" track-by="name" :preselect-first="true">
                  <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} users selected</span></template>
                </multiselect>
                <div
                  class="error-message"
                  v-if="form.errors.has('assign_to')"
                  v-html="form.errors.get('assign_to')"
                />
              </div>

              <div class="form-group">
                <label>Completed?</label>
                <input
                  type="checkbox"
                  true-value="1"
                  false-value="0"
                  v-model="form.is_completed"
                  name="is_completed"
                  class="form-control"
                />
              </div>
              
            </div>
            <div class="modal-footer">
              <button
                @click.prevent="addTask"
                v-if="editMode === false"
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.CREATE_TASK") }}
              </button>
              <button
                @click.prevent="editTask"
                v-else
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.EDIT_TASK") }}
              </button>

              <button type="button" class="btn btn-danger" data-dismiss="modal">
                {{ $t("message.CLOSE") }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Vue from 'vue'
import Multiselect from 'vue-multiselect';
Vue.component('multiselect', Multiselect);
export default {
  name: "addEditTaskModal",
  props: ["taskdata"],
  data() {
    return {
      currentLanguage: dateLanguage,
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      scholars: [],
      tasks: [],
      centers: [],
      mosques: [],
      users: [],
      editMode: "",
      hide: false,
      // Create a new form instance
      form: new form({
        id: "",
        name: '',
        assign_to: [],
        is_completed: false,
      }),
    };
  },
  methods: {
    addTask() {
      if (this.is("Super Admin") || this.can("create_task")) {
        this.$Progress.start();

        this.form
          .post("api/tasks")
          .then(() => {
            Fire.$emit("reloadTasks");
            $("#addEditTaskModal").modal("hide");
            toast.fire({
              icon: "success",
              title: this.$t("message.CREATED_MESSAGE_SUCCESS"),
            });
            this.$Progress.finish();
          })
          .catch(() => {
            this.$Progress.fail();
            toast.fire({
              icon: "warning",
              title: this.$t("message.CREATED_MESSAGE_ERROR"),
            });
          });
      } else {
        swal.fire({
          text: this.$t("message.UNAUTHORIZED"),
          icon: "warning",
        });
      }
    },
    editTask() {
      if (this.is("Super Admin") || this.can("edit_task")) {
        this.$Progress.start();
        this.form
          .put("api/tasks/" + this.form.id)
          .then(() => {
            Fire.$emit("reloadTasks");
            $("#addEditTaskModal").modal("hide");
            toast.fire({
              icon: "success",
              title: this.$t("message.EDIT_MESSAGE_SUCCESS"),
            });
            this.$Progress.finish();
          })
          .catch(() => {
            this.$Progress.fail();
            toast.fire({
              icon: "warning",
              title: this.$t("message.EDIT_MESSAGE_ERROR"),
            });
          });
      } else {
        swal.fire({
          text: this.$t("message.UNAUTHORIZED"),
          icon: "warning",
        });
      }
    },

    hideFile()
    {
      this.hide = true;
    }
   
  },
  mounted() {
    var form = this.form;
    var that = this;
    $("#addEditTaskModal").on("show.bs.modal", function (e) {
      that.hide = false;
      if (e.relatedTarget) {
        that.editMode = true;
        form.fill(e.relatedTarget);
        const users = []
        e.relatedTarget.users?.map(item => {
          users.push(item);
        })
        form.assign_to = users;
      } else {
        form.reset();
        that.editMode = false;
      }
    });

    //get all users
    if(this.is('Super Admin') || this.can('create_task') || this.can('edit_task'))
    {
        axios.get('/api/all-users')
        .then((response)=>{
            this.users = response.data;
        })
    };

  },
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>