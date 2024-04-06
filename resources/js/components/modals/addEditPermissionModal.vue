<template>
  <div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="addEditPermissionModal"
      permission="dialog"
      aria-labelledby="addEditPermissionModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              class="modal-title"
              id="addEditPermissionModalLabel"
              v-if="editMode === false"
            >
              {{ $t("message.CREATE_PERMISSION") }}
            </h5>
            <h5 class="modal-title" id="addEditPermissionModalLabel" v-else>
              {{ $t("message.EDIT_PERMISSION") }}
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
          <form @submit.prevent="editMode ? editPermission() : addPermission()">
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
            </div>
            <div class="modal-footer">
              <button
                @click.prevent="addPermission"
                v-if="editMode === false"
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.CREATE_PERMISSION") }}
              </button>
              <button
                @click.prevent="editPermission"
                v-else
                type="submit"
                class="btn btn-primary"
              >
                {{ $t("message.EDIT_PERMISSION") }}
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
  name: "addEditPermissionModal",
  props: ["permissiondata"],
  data() {
    return {
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      permissions: [],
      editMode: "",
      // Create a new form instance
      form: new form({
        id: "",
        name: "",
      }),
    };
  },
  methods: {
    addPermission() {
      if (this.is("Super Admin") || this.can("create_permission")) {
        this.$Progress.start();
        this.form
          .post("api/permissions")
          .then(() => {
            Fire.$emit("reloadPermissions");
            $("#addEditPermissionModal").modal("hide");
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
    editPermission() {
      if (this.is("Super Admin") || this.can("edit_permission")) {
        this.$Progress.start();
        this.form
          .put("api/permissions/" + this.form.id)
          .then(() => {
            Fire.$emit("reloadPermissions");
            $("#addEditPermissionModal").modal("hide");
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
    $("#addEditPermissionModal").on("show.bs.modal", function (e) {
      if (e.relatedTarget) {
        that.editMode = true;
        form.fill(e.relatedTarget);
      } else {
        form.reset();
        that.editMode = false;
      }
    });
  },
};
</script>