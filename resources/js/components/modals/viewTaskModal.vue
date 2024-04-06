<template>
  <div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="viewTaskModal"
      role="dialog"
      aria-labelledby="viewTask"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-dialog-centered modalLarge"
        role="document"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewTaskModal">
              View Task
              {{ taskData.name }}
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
          <div class="modal-body">
            <!-- Print Data -->
            <div id="printData">
              <div class="onlyForPrint">
                <printHeader></printHeader>
                <div class="printHeading">
                  {{ $t("message.VIEW_TASK") }}:
                  {{ taskData.name }}
                </div>
              </div>
              <table class="table table-bordered census-table">
                <tbody>
                  <tr>
                    <th>{{ $t("message.NAME") }}</th>
                    <td v-if="taskData.name">{{ taskData.name }}</td>
                    <td v-else>-</td>
                  </tr>
                  <tr>
                    <th>{{ $t("message.USER") }}</th>
                    <td v-if="taskData.user">
                      {{ taskData.user.name }}
                    </td>
                    <td v-else>-</td>
                  </tr>
                  <tr>
                    <th>{{ 'ASSIGN_TO' }}</th>
                    <td v-if="taskData.users && taskData.users.length>0">
                        <div v-for="user in taskData.users" :key="user.id">
                            {{user.name}}
                        </div>
                    </td>
                </tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">
                {{ $t("message.CLOSE") }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import printHeader from "../includes/printHeader.vue";
export default {
  name: "viewTaskModal",
  props: ["taskData"],
  data() {
    return {
      host: "",
    };
  },
  components: {
    printHeader,
  },
  methods: {
    printSale() {
      this.$htmlToPaper("printData");
    },

    // function to download the file
    downloadItem(url, label) {
      this.host = window.location.protocol + "//" + window.location.host;
      // call the api to get file
      axios({
        url: url,
        method: "GET",
        responseType: "blob",
      }).then((response) => {
        var fileURL = window.URL.createObjectURL(new Blob([response.data]));
        var fileLink = document.createElement("a");
        var extension = label.split(".").pop();

        fileLink.href = fileURL;
        fileLink.setAttribute("download", "file." + extension);
        document.body.appendChild(fileLink);

        fileLink.click();
      });
    },

    // delete the file
    deleteFile(id) {
      if (this.is("Super Admin") || this.can("delete_user")) {
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
              axios
                .get("api/deleteTaskFile/" + id)
                .then(() => {
                  Fire.$emit("reloadTasks");
                  this.$Progress.finish();
                  swal.fire(
                    this.$t("message.DELETED"),
                    this.$t("message.DELETE_MESSAGE_SUCCESS"),
                    "success"
                  );
                  $("#viewTaskModal").modal("hide");
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

    // edit the file
    editFile(file)
    {
        if (this.is("Super Admin") || this.can("edit_user")) {
            $("#uploadFileModal").modal("show", file);
          } else {
            toast.fire({
              icon: "error",
              title: this.$t("message.UNAUTHORIZED"),
            });
          }
    },

    // Get userdata by Id
    getTaskById(id)
    {
       if (this.is("Super Admin") || this.can("edit_user")) 
       {
          axios
            .get("api/getTaskById/" + id)
            .then((response) => {
              this.taskData = response.data;
            })
       }
    },


  },

   created() {
    var that = this;
      Fire.$on("reloadTaskData", () => {
        if(that.taskData.id != null || that.taskData.id != '')
        {
          that.getTaskById(that.taskData.id);
        }
      });

  },

};
</script>