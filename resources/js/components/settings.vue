<template>
  <v-app>
    <div class="content-wrapper">
      <div v-if="is('Super Admin') || can('application')">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">{{ $t("message.APPLICATION") }}</h1>
              </div>
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
                    <h3 class="card-title">
                      {{ $t("message.APPLICATION") }}
                    </h3>
                  </div>
                  <!-- /.card-header -->
                  <form @submit.prevent>
                    <input type="hidden" name="_token" :value="csrf" />
                    <div class="card-body">
                      <div class="form-group">
                        <label>
                          {{ $t("message.COMPANY_NAME") }}
                          <span class="required-star">*</span>
                        </label>
                        <input
                          autofocus
                          v-model="form.company_name"
                          v-bind:placeholder="$t('message.COMPANY_NAME')"
                          type="text"
                          name="company_name"
                          class="form-control"
                          :class="{
                            'is-invalid': form.errors.has('company_name'),
                          }"
                        />
                        <div
                          class="error-message"
                          v-if="form.errors.has('company_name')"
                          v-html="form.errors.get('company_name')"
                        />
                      </div>
                      <div class="form-group">
                        <label>{{ $t("message.LOGO") }}</label>
                        <input
                          @change="addLogo"
                          type="file"
                          name="logo"
                          class="form-control"
                          :class="{ 'is-invalid': form.errors.has('logo') }"
                        />
                        <div
                          class="error-message"
                          v-if="form.errors.has('logo')"
                          v-html="form.errors.get('logo')"
                        />
                      </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                      <button
                        @click.prevent="saveSettings"
                        type="submit"
                        class="btn btn-primary"
                      >
                        {{ $t("message.SAVE") }}
                      </button>
                    </div>
                  </form>
                </div>
                <!-- /.card -->
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
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
export default {
  data() {
    return {
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      loading: true,
      form: new form({
        id: "",
        company_name: "",
        logo: "",
      }),
    };
  },
  methods: {
    getResults() {
      if (this.is('Super Admin') || this.can('application')) {
      axios
        .get("api/settings")
        .then((response) => {
          this.$Progress.start();
          this.loading = true;
          this.settings = response.data;
          var form = this.form;
          form.fill(this.settings);
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
      }else{
        toast.fire({
          icon: "error",
          title: this.$t("message.UNAUTHORIZED")
        });
        this.$Progress.fail();
        this.loading = false;
      }
    },
    saveSettings() {
      if (this.is('Super Admin') || this.can('application')) {
        this.$Progress.start();
        this.form
          .put("api/settings/" + this.form.id)
          .then(() => {
            Fire.$emit("reloadSettings");
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
      }else{
        toast.fire({
          icon: "error",
          title: this.$t("message.UNAUTHORIZED")
        });
        this.$Progress.fail();
        this.loading = false;
      }
    },
    addLogo(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      reader.onloadend = () => {
        this.form.logo = reader.result;
      };
      reader.readAsDataURL(file);
    },
  },
  mounted() {
    this.getResults();
    Fire.$on("reloadSettings", () => {
      this.getResults();
    });
  },
};
</script>