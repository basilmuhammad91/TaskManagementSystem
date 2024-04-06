const settingsModule={
    state: {
        settings: [],
    },
    mutations: {
        setSettings(state, payload){
            state.settings=payload;
        }
    },
    actions: {
        getSettingsFromAPI(context){
            axios.get('api/settings')
                .then(response => {
                    context.commit('setSettings', response.data)
                });
        },
    },
    getters: {
        settings: state=>{
            return state.settings;
        },
    }
};
export default settingsModule;