import RegisterFishpond from './RegisterFishpond';
import costForm from './costForm';

//CONFIGURACIÓN DE DAYJS
const dayjs = require('dayjs');
require('dayjs/locale/es-do');

//Se adiciona el pluging para tiempo relativo
let relativeTime = require('dayjs/plugin/relativeTime');
dayjs.extend(relativeTime);

let isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

//Se establece en español
dayjs.locale('es-do');

window.dayjs = dayjs;
window.registerForm = RegisterFishpond;
window.costForm = costForm;

window.app = () => {
  return {
    /** Listado de estanques recuperados del servidor */
    fishponds: [],
    /** Instancia de estanques para visualizar los detalles o agregar costos */
    fishpondSelected: undefined,
    /** Establece si se están recuperando los datos de los estanques del servidor */
    updatingModel: false,
    /** Define si se estpa mostrando algun modal actualmente */
    showingModal: false,
    /** Define si el modal que se está mostrando es el de registro de estanque */
    showingRegisterModal: false,
    /** Habilita o deshabilita la vista para visualizar los cotos del estanque */
    showingCosts: false,
    /** Habilita o deshabilita el modal para registrar o actualizar un costo */
    showingCostForm: false,
    /** Se encarga de disparar eventos personalizados */
    dispatch: undefined,
    /** Se encarga de las peticiones al servidor */
    wire: undefined,
    /** Contiene los valores de los distintos tipos de costos */
    costType: {
      materials: 'Costo de materiales',
      workforce: 'Mano de obra',
      maintenance: 'Costo de mantenimiento',
    },
    // *============================================================================================*
    // *===================================== INICIALIZACION =======================================*
    // *============================================================================================*
    /**
     * Este metodo se encarga de crear los objetos ecesarios para que el componente pueda funcionar
     * @param {*} wire Instancia de livewire para comunicarse con el servidor
     * @param {*} dispatch Instancia de alpine para generar eventos personalizados
     */
    init(wire, dispatch) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.updateModel();
    },
    /**
     * Se encarga de hacer una petición al servidor para recuperar
     * la informacion de los estanques y agregarlas al componente
     */
    updateModel() {
      //Se notifica que el componente se está actualizando
      this.updatingModel = true;
      //Se realiza la petición al servidor
      this.wire.getFishponds().then(res => {
        res.forEach(data => {
          //Se formatean las fechas
          data.costs.map(cost => {
            this.formatCostDate(cost);
            return cost;
          })

          //Se organizan los costos de mas antigua a mas reciente
          this.sortFishpondCost(data);
          //Se agregan al listado de estanques
          this.addNewFishpond(data);
        });

        this.updatingModel = false;
      }).catch(error => {
        console.log(error);
        this.updatingModel = false;
      })
    },


    // *============================================================================================*
    // *=============================== RELACIONADOS CON LOS CRUDS =================================*
    // *============================================================================================*
    /**
     * Se encarga de mostrar el modal para la catualización del estanque
     * y de notificarle al componente los datos del mismo.
     * @param {*} fishpond Instancia de estanque
     */
    editFishpond(fishpond) {
      this.showRegisterForm();
      this.dispatch('edit-fishpond', fishpond);
    },
    /**
     * Se encarga de mostrar el formulario de costos y notificar 
     * los datos del costo a adeitar
     * @param {*} cost Instancia de costo
     */
    editCost(cost) {
      this.showCostForm();
      this.dispatch('edit-cost', cost);
    },
    /**
         * Se encarg de agregar la nueva instancia de estanque al listado
         * @param {*} data Instancia de estanque
         */
    addNewFishpond(data) {
      this.fishponds.push(data);
    },
    /**
     * Se encarga de buscar el estanque al que se le va a gregar el costo registrado
     * y posteriormente actualizar el orden de los mismos
     * @param {*} data Instancia de costo
     */
    addNewFishpondCost(data) {
      //Se busca el estanque que tiene el nuevo costo
      let fishpond = this.fishponds.find(f => f.id === data.fishpondId);
      if (fishpond) {
        //Se formatean las fechas del costo
        this.formatCostDate(data);

        //Se agrega el costo al arreglo
        fishpond.costs.push(data);
        //Se actualiza el importe
        fishpond.costsAmount += data.amount;
        console.log(fishpond);
        //Se ordenan los costos de mas antigua a mas reciente
        this.sortFishpondCost(fishpond);
      }
    },
    /**
     * Se encarga de actualizar los valores de cada uno de los campos 
     * del estanque y que estos corresponda con los datos del servidor
     * @param {*} data Instnacia de un estanque actualizado
     */
    updateFishpond(data) {
      //Recupero la instancia del estanque actualizado
      let fishpond = this.fishponds.find(x => x.id === data.id);
      if (fishpond) {
        for (const key in data) {
          if (Object.hasOwnProperty.call(fishpond, key)) {
            fishpond[key] = data[key];
          }
        }

        //Se actualizan las fechas de los costos
        fishpond.costs.map(c => {
          this.formatCostDate(c);
            return c;
        })
      }
    },
    updateFishpondCost(data) {
      //Recupero la instancia del estanque
      let fishpond = this.fishponds.find(f => f.id === data.fishpondId);
      if (fishpond) {
        //Se recupera la instancia del costo a actualizar
        let cost = fishpond.costs.find(c => c.id === data.id);
        if (cost) {
          //descuento el valor del importe
          fishpond.costsAmount -= cost.amount;
          //Se formatean las fecha del los datos
          this.formatCostDate(data);
          //Se actualizan los datos del importe
          for (const key in data) {
            if (Object.hasOwnProperty.call(cost, key)) {
              cost[key] = data[key];
            }
          }
          console.log(cost);
          //Adiciono el valor del importe al estanque
          fishpond.costsAmount += cost.amount;
          //Se ordena los costos
          this.sortFishpondCost(fishpond);
        }//.end if(2)
      }//.end if(1)
    },//.end method
    destroyFishpond(id) {
      window.Swal.fire({
        title: "¿Desea eliminar este estanque?",
        text: "Esta acción no puede revertirse y eliminará toda la información del estanque junto con sus registros de costos.",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyFishpond(id).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.isOk) {
            //Recupero el index del estanque
            let index = this.fishponds.findIndex(f => f.id === id);
            //Se elimina el estanque del arreglo
            if (index >= 0) {
              this.fishponds.splice(index, 1);
            }
          } else {
            if (result.result.errors.notFund) {
              location.reload();
            }
          }
        }
      });
    },
    destroyFishpondCost(costId, fishpondId) {
      window.Swal.fire({
        title: "¿Desea eliminar este costo?",
        text: "Esta acción no puede revertirse ¿Está seguro que desea continuar?",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Si, Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyFishpondCost(fishpondId, costId).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.isOk) {
            //Recuperar el estanque
            let fishpond = this.fishponds.find(f => f.id === fishpondId);
            if(fishpond){
              //Se recupera el index del costo a eliminar
              let costIndex = fishpond.costs.findIndex(c => c.id === costId);
              if (costIndex >= 0) {
                //Se descuenta el importe del costo del estanque
                fishpond.costsAmount -= fishpond.costs[costIndex].amount;
                //Se elimina el costo del listado
                fishpond.costs.splice(costIndex, 1);
              }
            }
          } else {
            if (result.result.errors.notFund) {
              location.reload();
            }
          }
        }
      });
    },
    // *============================================================================================*
    // *=========================== ADMINISTRACIÓN DE VISTAS Y MODALES =============================*
    // *============================================================================================*
    /**
     * Muestra el formulario para registrar nuevos estanques
     */
    showRegisterForm() {
      this.showingModal = true;
      this.showingRegisterModal = true;
    },
    /**
     * Muestra el formulario de costos para registrar un
     * nuevo costo al estanque o para actualizar uno existente
     */
    showCostForm() {
      this.showingModal = true;
      this.showingCostForm = true;
    },
    /**
     * Este metodo muestra la vista con los datos del estanque
     * para poder visualizar los costos registrados
     * @param {*} data Instancia de estanque a guardar en memoria
     */
    showCosts(data) {
      this.dispatch('fishpond-selected', data);
      this.fishpondSelected = data;
      this.showingCosts = true;
    },
    /**
     * Se encarga de ocultar todos los modales del componente
     */
    hiddenModal() {
      this.showingRegisterModal = false;
      this.showingCostForm = false;
      this.showingModal = false;
    },
    // *============================================================================================*
    // *======================================= UTILIDADES =========================================*
    // *============================================================================================*
    /**
     * Este metodo transforma el objeto original para relativisar las fechas de
     * creación y re registro, asi como para darle un formato a las mismas.
     * @param {*} data Instancia de costo procedente del servidor
     * @returns {*}
     */
    formatCostDate(data) {
      data.fullDate = `${data.date} ${data.time}`;
      data.dateFormat = dayjs(data.date).format('dddd DD/MM/YY');
      data.fromNow = dayjs(data.fullDate).fromNow();
      data.createdAt = dayjs(data.createdAt).fromNow();
      data.updatedAt = dayjs(data.updatedAt).fromNow();
    },
    /**
     * Se encarga de ordenar los costos del estanque del mas antiguo al mas reciente.
     * @param {*} fishpond Instancia del estanque que se va a ordenar
     */
    sortFishpondCost(fishpond) {
      fishpond.costs.sort((a, b) => {
        let date1 = dayjs(a.fullDate);
        let date2 = dayjs(b.fullDate);

        if (date1.isBefore(date2)) {
          return -1;
        } else if (date1.isSame(date2)) {
          return 0
        } else {
          return 1;
        }
      })
    },
  }
}
