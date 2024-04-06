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
              {{ $t("message.VIEW_TASK") }}:
              {{ buildingData.name }}
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
                  {{ buildingData.name }}
                </div>
              </div>
              <table class="table table-bordered census-table">
                <tbody>
                  <tr>
                    <th>{{ $t("message.NAME") }}</th>
                    <td v-if="buildingData.name">{{ buildingData.name }}</td>
                    <td v-else>-</td>
                  </tr>
                  <tr>
                    <th>{{ $t("message.DESCRIPTION") }}</th>
                    <td v-if="buildingData.description">
                      {{ buildingData.description }}
                    </td>
                    <td v-else>-</td>
                  </tr>
                  <tr>
                    <th>{{ $t("message.CITY") }}</th>
                    <td v-if="buildingData.city">
                      {{ buildingData.city.name }}
                    </td>
                    <td v-else>-</td>
                  </tr>
                  <tr>
                    <th>{{ $t("message.CENTER") }}</th>
                    <td v-if="buildingData.center">
                      {{ buildingData.center.name }}
                    </td>
                    <td v-else>-</td>
                  </tr>
                  <tr>
                    <th>{{ $t("message.CREATED_AT") }}</th>
                    <td v-if="buildingData.created_at">
                      {{ buildingData.created_at | formatDate }}
                    </td>
                    <td v-else>-</td>
                  </tr>
                  <tr v-if="buildingData.updated_at">
                    <th>{{ $t("message.UPDATED_AT") }}</th>
                    <td>{{ buildingData.updated_at | formatDate }}</td>
                  </tr>
                </tbody>
              </table>
              <h3>{{ $t("message.ATTACHMENT_DETAILS") }}</h3>
              <table class="table table-bordered mt-1">
                <tbody>
                  <tr>
                    <th>
                      {{ this.$t("message.TITLE") }}
                    </th>
                    <th>
                      {{ this.$t("message.DESCRIPTION") }}
                    </th>
                    <th class="no-print">
                      {{ this.$t("message.FILES") }}
                    </th>
                    <th class="no-print">
                      {{ this.$t("message.ACTIONS") }}
                    </th>
                  </tr>
                  <tr v-for="(file, item) in buildingData.files" :key="file.id">
                    <td v-if="file.title">{{ file.title }}</td>
                    <td v-else>-</td>
                    <td v-if="file.description">{{ file.description }}</td>
                    <td v-else>-</td>
                    <td v-if="file.filename" class="no-print">
                      <a
                        download
                        target="_blank"
                        :href="host+'../storage/' + file.filename"
                        >{{ $t("message.DOWNLOAD_FILE") }}</a
                      >
                    </td>
                    <td v-else>-</td>
                    <td>

                      <!-- Edit File -->
                      <v-icon
                          color="green"
                          class="edit-icon"
                          small
                          @click="editFile(file)"
                          v-if="is('Super Admin') || can('edit_building') && item > 0"
                        >
                          mdi-pencil
                        </v-icon>

                      <!-- Delete File -->
                       <v-icon
                          color="red"
                          class="delete-icon"
                          small
                          @click="deleteFile(file.id)"
                          v-if="is('Super Admin') || can('delete_building') && item > 0"
                        >
                          mdi-delete
                        </v-icon>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button @click.prevent="printSale" class="btn btn-primary">
                {{ $t("message.PRINT") }}
              </button>
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
  props: ["buildingData"],
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
      if (this.is("Super Admin") || this.can("delete_building")) {
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
        if (this.is("Super Admin") || this.can("edit_building")) {
            $("#uploadFileModal").modal("show", file);
          } else {
            toast.fire({
              icon: "error",
              title: this.$t("message.UNAUTHORIZED"),
            });
          }
    },

    // Get buildingdata by Id
    getTaskById(id)
    {
       if (this.is("Super Admin") || this.can("edit_building")) 
       {
          axios
            .get("api/getTaskById/" + id)
            .then((response) => {
              this.buildingData = response.data;
            })
       }
    },


  },

   created() {
    var that = this;
      Fire.$on("reloadTaskData", () => {
        if(that.buildingData.id != null || that.buildingData.id != '')
        {
          that.getTaskById(that.buildingData.id);
        }
      });

  },

};
</script>