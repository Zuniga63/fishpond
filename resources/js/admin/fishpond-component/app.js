import RegisterFishpond from './RegisterFishpond';

window.registerForm = () => {
  return RegisterFishpond;
} 
  



window.app = ()=>{
  return{
    showingModal: false,
    wire: undefined,
    dispatch: undefined,
    fishponds: [],
    updatingModel: false,
    addNewFishpond(data){
      this.fishponds.push(data);
    },
    updateFishpond(data){
      //Recupero la instancia del estanque actualizado
      let fishpond = this.fishponds.find(x => x.id === data.id);
      if(fishpond){
        for (const key in data) {
          if (Object.hasOwnProperty.call(data, key)) {
            fishpond[key] = data[key];
          }
        }
      }
    },
    init(wire, dispatch){
      this.wire = wire;
      this.dispatch = dispatch;
      this.updateModel();
    },
    editFishpond(fishpond){
      this.showingModal = true;
      this.dispatch('edit-fishpond', fishpond);
    },
    updateModel(){
      this.updatingModel = true;
      this.wire.getFishponds().then(res => {
        res.forEach(data => {
          this.addNewFishpond(data);
        });

        this.updatingModel = false;
      }).catch(error => {
        console.log(error);
        this.updatingModel = false;
      })
    },
  }
}
