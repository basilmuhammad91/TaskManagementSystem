<template>
  <div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="addEditRoleModal"
      role="dialog"
      aria-labelledby="addEditRoleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              class="modal-title"
              id="addEditRoleModalLabel"
              v-if="editMode === false"
            >
              {{ $t("message.CREATE_ROLE") }}
            </h5>
            <h5 class="modal-title" id="addEditRoleModalLabel" v-else>
              {{ $t("message.EDIT_ROLE") }}
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
          <form @submit.prevent="editMode ? editRole() : addRole()">
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
              <h6>{{ $t("message.PERMISSIONS") }}</h6>
              <b-form-group>
                <b-form-checkbox
                  v-for="permission in permissions"
                  v-model="form.permissions"
                  :key="permission.name"
                  :value="permission"
                  name="flavour-3a"
                >
                  {{ permission.name }}
                </b-form-checkbox>
              </b-form-group>
            </div>
            <div class="modal-footer">
              <button
                @click.prevent="addRole"
                v-if="editMode === false"
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.CREATE_ROLE") }}
              </button>
              <button
                @click.prevent="editRole"
                v-else
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.EDIT_ROLE") }}
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
  name: "addEditRoleModal",
  props: ["roledata"],
  data() {
    return {
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      roles: [],
      permissions: [],
      editMode: "",
      // Create a new form instance
      form: new form({
        id: "",
        name: "",
        permissions: [],
      }),
    };
  },
  methods: {
    addRole() {
      if (this.is("Super Admin") || this.can("create_role")) {
        this.$Progress.start();
        this.form
          .post("api/roles")
          .then(() => {
            Fire.$emit("reloadRoles");
            $("#addEditRoleModal").modal("hide");
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
    editRole() {
      if (this.is("Super Admin") || this.can("edit_role")) {
        this.$Progress.start();
        this.form
          .put("api/roles/" + this.form.id)
          .then(() => {
            Fire.$emit("reloadRoles");
            $("#addEditRoleModal").modal("hide");
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
  },
  mounted() {
    var form = this.form;
    var that = this;
    $("#addEditRoleModal").on("show.bs.modal", function (e) {
      form.reset();
      that.permissions = [];
      if (e.relatedTarget) {
        that.editMode = true;
        form.name = e.relatedTarget.name;
        form.id = e.relatedTarget.id;
        if (
          e.relatedTarget.permissions &&
          e.relatedTarget.permissions.length > 0
        ) {
          e.relatedTarget.permissions.map((res) => {
            form.permissions.push({
              id: res.id,
              name: res.name,
            });
          });
        }
      } else {
        form.reset();
        that.editMode = false;
      }
      that.$Progress.start();
      axios
        .get("api/permissions")
        .then((response) => {
          response.data.map((res) => {
            that.permissions.push({
              id: res.id,
              name: res.name,
            });
          });
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