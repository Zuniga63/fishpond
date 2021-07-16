import { isEmpty } from 'lodash';
import input from '../input';

window.fishBatchObservationForm = () => {
  return {
    /** Esta variable determina si se muestra o no el formulario en pantalla */
    visible: false,
    /**
     * Determina el tipo de formulario
     * register: Para nueva observación
     * updating: Para actualizar la observación
     */
    mode: 'register',
    /** Instancia del lote que se desea actualizar */
    fishBatch: null,
    /** Instancia de la oservación a actualizar */
    originalObservation: null,
    // *===========================================*
    // *========== Campos del formulario ==========*
    // *===========================================*
    /** Corresponde al titulo de la observación */
    title: null,
    /** Corresponde al mensaje que se desea guardar */
    message: null,
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
    // *===============================================*
    // *============ Metodos del Componente ===========*
    // *===============================================*
    /**
     * Se encarga de montar los datos iniciales
     * @param {*} wire Objeto de livewire encargado de las peticiones
     * @param {*} dispatch Onjeto de alpine encargado de los eventos
     * @param {*} refs Objeto de alpine encargado de las referencias
     */
    init(wire = null, dispatch = null, refs = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
      this.__createInputs();
    },
    /**
     * Se encarga de crear las intancias de los campos del formulario
     */
    __createInputs() {
      this.title = input({
        id: 'observationTitle',
        name: 'title',
        label: 'Titulo',
        placeholder: 'Escribe el titulo aquí.',
        min: 3,
        max: 45,
      });
      this.message = input({
        id: 'observationMessage',
        name: 'message',
        label: 'Observación',
        placeholder: 'Escribe la observación aquí',
        min: 3,
        max: 255,
      });
    },
    reset() {
      this.visible = false;
      this.mode = "register";
      this.fishBatch = null;
      this.originalObservation = null;
      //Se resetean los campos
      this.title.reset();
      this.message.reset();
    },
    enableForm(data) {
      this.visible = true;
      this.mode = data.mode;
      this.fishBatch = data.fishBatch;
      if (this.mode === 'updating') {
        this.originalObservation = data.data;
        this.title.value = data.data.title;
        this.message.value = data.data.message;
      }
    },
    /**
     * Resetea el formulario y se encarga de emitir el evento
     * de que no he ha realizado ninguna acción
     */
    cancel() {
      this.reset();
      this.dispatch('cancel-form-operation');
    },
    // *===============================================*
    // *================= PETICIONES ==================*
    // *===============================================*
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
      this.wire.storeObservation(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              observation: res.observation,
            }
            //Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('observation-was-created', info);
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
      this.wire.updateObservation(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              observation: res.observation,
            }
            //Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('observation-was-updated', info);
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
    /**
     * Crea un objeto con los datos requeridos por el servidor
     * ya sea para registrar un nuevo lote o para actualizarlo.
     * @returns {*}
     */
    getSubmitData() {
      let data = {
        title: this.title.value,
        message: this.message.value,
        fishBatchId: this.fishBatch?.id,
      }

      if (this.mode === 'updating') {
        data.observationId = this.originalObservation?.id;
      }

      return data;
    },
    // *===============================================*
    // *================ VALIDACIONES =================*
    // *===============================================*
    validateTitle() {
      let value = this.title.value;
      let min = this.title.min;
      let max = this.title.max;
      let message = null;
      if (!isEmpty(value)) {
        if (value.length >= min) {
          if (value.length <= max) {
            this.title.isOk();
            return true;
          } else {
            message = `El titulo debe tener menos de ${max} caracteres`;
          }
        } else {
          message = `El titulo debe tener almeno ${min} caracteres.`
        }
      } else {
        message = `Este campo es obligatorio.`;
      }

      this.title.setError(message);
      return false;
    },
    validateMessage() {
      let value = this.message.value;
      let min = this.message.min;
      let max = this.message.max;
      let message = null;
      if (!isEmpty(value)) {
        if (value.length >= min) {
          if (value.length <= max) {
            this.message.isOk();
            return true;
          } else {
            message = `El titulo debe tener menos de ${max} caracteres`;
          }
        } else {
          message = `El titulo debe tener almeno ${min} caracteres.`
        }
      } else {
        message = `Este campo es obligatorio.`;
      }

      this.message.setError(message);
      return false;
    },
    /**
     * Realiza la validación de todos los campos y retorna true si todos
     * son correctos.
     * @returns {boolean}
     */
    validateSubmit() {
      let validations = [];
      validations.push(this.validateTitle());
      validations.push(this.validateMessage());

      //Retorna false si alguna de las validaciones es falsa, pero valida todos los campos
      return !validations.some(val => val === false);
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
    // *===============================================*
    // *================= UTILIDADES ==================*
    // *===============================================*
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
  }
}