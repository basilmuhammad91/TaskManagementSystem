<template>
  <div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="addEditUserModal"
      role="dialog"
      aria-labelledby="addEditUserModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              class="modal-title"
              id="addEditUserModalLabel"
              v-if="editMode === false"
            >
              {{ $t("message.CREATE_USER") }}
            </h5>
            <h5 class="modal-title" id="addEditUserModalLabel" v-else>
              {{ $t("message.EDIT_USER") }}
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
          <form @submit.prevent="editMode ? editUser() : addUser()">
            <input type="hidden" name="_token" :value="csrf" />
            <div class="modal-body">
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
                <label
                  >{{ $t("message.EMAIL")
                  }}<span class="required-star">*</span></label
                >
                <input
                  v-model="form.email"
                  v-bind:placeholder="$t('message.EMAIL')"
                  type="email"
                  name="email"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('email') }"
                />
                <div
                  class="error-message"
                  v-if="form.errors.has('email')"
                  v-html="form.errors.get('email')"
                />
              </div>
              <div class="form-group">
                <label
                  >{{ $t("message.PASSWORD")
                  }}<span class="required-star">*</span></label
                >
                <input
                  v-model="form.password"
                  v-bind:placeholder="$t('message.PASSWORD')"
                  type="password"
                  name="password"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('password') }"
                />
                <div
                  class="error-message"
                  v-if="form.errors.has('password')"
                  v-html="form.errors.get('password')"
                />
              </div>
              <!-- Role Dropdown -->
              <div class="form-group">
                <label>{{ $t("message.ROLE") }}</label>
                <b-form-select
                  v-model="form.role"
                  :options="roles"
                  text-field="name"
                  value-field="id"
                ></b-form-select>
                <div
                  class="error-message"
                  v-if="form.errors.has('role')"
                  v-html="form.errors.get('role')"
                />
              </div>
              <div class="form-group">
                <label>{{ $t("message.PHOTO") }}</label>
                <input
                  @change="addPhoto"
                  type="file"
                  name="photo"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('photo') }"
                />
                <div
                  class="error-message"
                  v-if="form.errors.has('photo')"
                  v-html="form.errors.get('photo')"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button
                @click.prevent="addUser"
                v-if="editMode === false"
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.CREATE_USER") }}
              </button>
              <button
                @click.prevent="editUser"
                v-else
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.EDIT_USER") }}
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
  name: "addEditUserModal",
  data() {
    return {
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      roles: [],
      editMode: "",
      fileError: 0,
      roles: [],
      // Create a new form instance
      form: new form({
        id: "",
        name: "",
        username: "",
        email: "",
        password: "",
        photo: "",
        role: "",
      }),
    };
  },
  methods: {
    addUser() {
      if (this.is("Super Admin") || this.can("create_user")) {
        if (!this.fileError) {
          this.$Progress.start();
          this.form
            .post("api/users")
            .then(() => {
              Fire.$emit("reloadUsers");
              $("#addEditUserModal").modal("hide");
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
          this.$Progress.fail();
          toast.fire({
            icon: "warning",
            title: this.$t("message.FLIE_SIZE_ERROR"),
          });
        }
      } else {
        swal.fire({
          text: this.$t("message.UNAUTHORIZED"),
          icon: "warning",
        });
      }
    },
    editUser() {
      if (this.is("Super Admin") || this.can("edit_user")) {
        if (!this.fileError) {
          this.$Progress.start();
          this.form
            .put("api/users/" + this.form.id)
            .then(() => {
              Fire.$emit("reloadUsers");
              $("#addEditUserModal").modal("hide");
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
          this.$Progress.fail();
          toast.fire({
            icon: "warning",
            title: this.$t("message.FLIE_SIZE_ERROR"),
          });
        }
      } else {
        swal.fire({
          text: this.$t("message.UNAUTHORIZED"),
          icon: "warning",
        });
      }
    },
    addPhoto(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      reader.onloadend = () => {
        this.form.photo = reader.result;
      };
      reader.readAsDataURL(file);
    },
  },
  mounted() {
    var form = this.form;
    var that = this;
    $("#addEditUserModal").on("show.bs.modal", function (e) {
      if (e.relatedTarget) {
        that.editMode = true;
        form.fill(e.relatedTarget);
      } else {
        form.reset();
        that.editMode = false;
      }
      that.$Progress.start();
      axios
        .get("api/roles")
        .then((response) => {
          that.roles = response.data;
          that.$Progress.finish();
        })
        .catch(() => {
          that.$Progress.fail();
          toast.fire({
            icon: "error",
            title: that.$t("message.SOMETHING_WENT_WRONG"),
          });
        });
    });
  },
};
</script>