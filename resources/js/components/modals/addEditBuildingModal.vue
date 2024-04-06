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

             <!-- City Dropdown -->
              <div class="form-group">
                <label
                  >{{ $t("message.CITY")
                  }}<span class="required-star">*</span></label
                >
                <v-select
                  v-model="form.city_id"
                  :options="cities"
                  label="name"
                  :reduce="(city) => city.id"
                  :selectOnTab="true"
                  :key="form.city_id"
                  :class="{ 'is-invalid': form.errors.has('city_id') }"
                />
                <div
                  class="error-message"
                  v-if="form.errors.has('city_id')"
                  v-html="form.errors.get('city_id')"
                />
              </div>

              <!-- Center Hidden Field -->
              <div class="form-group" v-if="centers.length == 1">
                 <input
                  v-model="form.center_id"        
                  type="hidden"
                  name="center_id"
                  class="form-control"
                >
                </div>

              <!-- Center Dropdown -->
              <div class="form-group" v-else>
                <label>{{$t('message.CENTER')}}<span class="required-star">*</span></label>
                <v-select v-model="form.center_id" :options="centers"  label="name" :reduce="center => center.id" :selectOnTab="true"
                  :key="form.center_id"
                  :class="{ 'is-invalid': form.errors.has('center_id') }"/>
                <div
                  class="error-message"
                  v-if="form.errors.has('center_id')"
                  v-html="form.errors.get('center_id')"
                />
              </div>

                <!-- Description -->
              <div class="form-group">
                <label>
                  {{ $t("message.DESCRIPTION") }}
                </label>
                <textarea
                  v-model="form.description"
                  v-bind:placeholder="$t('message.DESCRIPTION')"
                  type="text"
                  name="description"
                  class="form-control"
                ></textarea>
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
      cities: [],
      editMode: "",
      hide: false,
      // Create a new form instance
      form: new form({
        id: "",
        name: '',
        mosque_id: "",
        scholar_id: '',
        center_id: '',
        link: '',
        file_description: '',
        description: '',
        filename: '',
        title: '',
        city_id: '',
      }),
    };
  },
  methods: {
    addTask() {
      if (this.is("Super Admin") || this.can("create_task")) {
        this.$Progress.start();

        // if user have only 1 center add into form.centeer_id automatically
        if(this.centers.length == 1)
        {
          this.form.center_id = this.centers[0].id;
        }

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
          .post("api/taskUpdate/" + this.form.id)
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
      } else {
        form.reset();
        that.editMode = false;
      }
    });

    //get all cities
    if(this.is('Super Admin') || this.can('create_task') || this.can('edit_task'))
    {
        axios.get('/api/getAllCities')
        .then((response)=>{
            this.cities = response.data;
        })
    };

    //get all centers
    if(this.is('Super Admin') || this.can('create_task') || this.can('edit_task'))
    {
        axios.get('/api/getAllCenters')
        .then((response)=>{
            this.centers = response.data;
        })
    }

  },
};
</script>