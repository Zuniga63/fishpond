import { round } from 'lodash';
import input from '../input';

window.fishBatchDeathForm = () => {
  return {
    /** Define si el formulario se muestra o no */
    visible: false,
    /** 
     * Establece el tipo de formulario, 
     * si register para nuevo costo o 
     * updating para actualizar uno existente 
     * */
    mode: 'register',
    /**
     * Instnacia del lote de peces en el que se desea 
     * crear o actualizar un costo.
     */
    fishBatch: null,
    /** Instancia a modicar de un registro de defunción */
    originalDeath: null,
    // *===========================================*
    // *========== Campos del formulario ==========*
    // *===========================================*
    deaths: null,
    mortality: null,
    // *====================================================*
    // *========== Relacionadas con el componente ==========*
    // *====================================================*
    /** Define si el componente está esperando una respuesta del servidor */
    waiting: false,
    /** Se encarga de las peticones al servidor */
    wire: null,
    /** Se encarga de administrar los eventos personalizados */
    dispatch: null,
    /** Permite acceder a las referencias del componente en el DOM */
    refs: null,
    // *=====================================================*
    // *============= METODOS DE INICIALIZACIÓN =============*
    // *=====================================================*
    init(wire = null, dispatch = null, refs = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
      this.__createInputs();
    },
    __createInputs() {
      //muertes
      this.deaths = input({
        id: 'fishBatchDeath',
        name: 'deaths',
        label: 'Peces muertos',
        placeholder: 'Ingresa el numero de peces muertos.',
        min: 1,
        max: null,
      });
    },
    // *=====================================================*
    // *================ METODOS DE UTILIDAD ================*
    // *=====================================================*
    reset() {
      this.visible = false;
      this.mode = "register";
      this.fishBatch = null;
      this.waiting = false;
      //Se rresetean los formulario
      this.deaths.reset();
      this.mortality = null;
    },
    /**
     * Este metodo establece los parametros basicos con lo que
     * se inicializa el formulario cuando es llamado por un evento global.
     * @param {*} detail Detalles del modo en el que se habilita el formulario
     */
    enableForm(detail) {
      this.visible = true;
      this.mode = detail.mode;
      this.fishBatch = detail.fishBatch;
      this.deaths.max = detail.fishBatch.population;

      if (detail.mode === 'updating') {
        this.originalDeath = detail.data;
        this.deaths.value = detail.data.deaths;
      }
    },
    cancel() {
      this.reset();
      this.dispatch('cancel-form-operation');
    },
    notifyErrors(errors) {
      //Esto se encarga de actualizar los campos del formulario
      for (const key in errors) {
        if (Object.hasOwnProperty.call(errors, key)) {
          const error = errors[key];
          if (Object.hasOwnProperty.call(this, key)) {
            this[key].setError(error);
          }
        }
      }
    },
    __printSubmitData(data) {
      let bodyLength = 60;
      let separator = '-';
      let header = '';
      let left = `+${separator}`;
      let right = `${separator}+`;
      let text = '';

      header = left + separator.repeat(bodyLength) + right + '\n';
      text = header;

      for (const key in data) {
        if (Object.hasOwnProperty.call(data, key)) {
          let value = data[key] ? data[key] : 'null';
          let keyLength = key.length;
          let valueLength = value.length;
          let line = `${key}: ${value}`;
          if (line.length <= bodyLength) {
            line += ' '.repeat(bodyLength - line.length);
            text += `| ${line} |\n`;
          } else {
            let first = line.slice(0, bodyLength - 1);
            let last = line.slice(bodyLength, 259);

            text += `| ${first} |\n`;
            text += '| ' + ' '.repeat(keyLength + 2);
            text += '| ' + " ".repeat(bodyLength - last.length) + ' |' + '\n'
          }
        }//end if
      }//end for
      text += header;
      console.log(text, data);
    },
    updateMortality(){
      if(!this.deaths.hasError && this.fishBatch.population > 0){
        let mortality = (this.deaths.value / this.fishBatch.population) * 100;
        this.mortality = round(mortality, 2);
      }else{
        this.mortality = null;
      }
    },
    // *======================================*
    // *================ CRUD ================*
    // *======================================*
    submit() {
      if (this.validateSubmit()) {
        if (this.mode === 'register') {
          this.__store();
        } else if (this.mode === 'updating') {
          this.__update();
        }
      }
    },
    __store() {
      this.waiting = true;
      let data = this.getSubmitData();
      this.wire.storeDeathReport(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              death: res.death,
            }
            // Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('death-was-stored', info);
            //Se resetea el formulario
            this.reset();
          } else {
            console.log(res.errors);
            this.notifyErrors(res.errors);
          }
        }).catch(error => {
          console.log(error);
        }).finally(() => {
          this.waiting = false;
        })
    },
    __update() {
      this.waiting = true;
      let data = this.getSubmitData();
      this.wire.updateDeathReport(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              death: res.death,
            }
            // Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('death-was-updated', info);
            //Se resetea el formulario
            this.reset();
          } else {
            console.log(res.errors);
            this.notifyErrors(res.errors);
          }
        }).catch(error => {
          console.log(error);
        }).finally(() => {
          this.waiting = false;
        })
    },
    getSubmitData() {
      let data = {
        fishBatchId: this.fishBatch.id,
        deaths: this.deaths.value,
      };

      if (this.mode === 'updating') {
        data.deathId = this.originalDeath.id;
      }

      return data;
    },
    // *===============================================*
    // *================ VALIDACIONES =================*
    // *===============================================*
    validateDeaths() {
      let value = this.deaths.value;
      let message = null;

      if (value && value > 0) {
        if(value >= this.deaths.min){
          if(value <= this.deaths.max){
            this.deaths.isOk();
            this.updateMortality();
            return true;
          }else{
            message = 'Las muertes superan la población del estanque.';
          }
        }else{
          message = "Se debe registrar almeno 1 muerte.";
        }
      } else {
        message = "Este campo es obligatorio.";
      }

      this.deaths.setError(message);
      this.mortality = null;
      return false;
    },
    validateSubmit() {
      let validations = [];
      validations.push(this.validateDeaths());
      //Retorna false si alguna de las validaciones es falsa, pero valida todos los campos
      return !validations.some(val => val === false);
    }
  }
}//.end object