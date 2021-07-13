import input from '../input';

//CONFIGURACIÓN DE DAYJS
const dayjs = require('dayjs');
let isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

window.fishBatchForm = () => {
  return {
    /** Esta variable determina si se muestra o no el formulario en pantalla */
    visible: true,
    /**
     * Determina el tipo de formulario
     * register: Para nuevo lote de peces
     * updating: Para actualizar un lote existente
     */
    mode: 'register',
    /** Todas las instancias de los estanques */
    allFishponds: [],
    /** Instancias de los estanque libres */
    fishponds: [],
    /** Instancia del lote que se desea actualizar */
    fishBatch: null,
    // *===========================================*
    // *========== Campos del formulario ==========*
    // *===========================================*
    /** Campo con el identificador del estanque donde se desea sembrar */
    fishpondId: null,
    /** Variable que habilita el ingreso de la fecha */
    inThisMoment: true,
    /** Fecha en la que que se realiza la siembra */
    date: null,
    /** Variable que habilita el ingreso de la hora */
    setTime: false,
    /** Hora en la que se realiza la siembra */
    time: null,
    /** Población inicial de la siembra */
    population: null,
    /** Peso promedio de los alevinos */
    averageWeight: null,
    /** Muestra en el formulario la biomasa del lote inicial */
    biomass: null,
    /** Costo del lote en pesos colombianos */
    amount: null,
    /** Es el valor en pesos de cada alevino */
    unitCost: null,
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
      this.__buildFishponds(window.initialData.fishponds);
    },
    /**
     * Se encarga de crear las intancias de los campos del formulario
     */
    __createInputs() {
      this.fishpondId = input({
        id: 'fishpondId',
        name: 'fishpondId',
        label: 'Estanque a Sembrar',
        placeholder: 'Selecciona un estanque',
      });

      this.date = input({
        id: 'seedtimeDate',
        name: 'date',
        label: 'Selecciona una fecha',
        required: true,
        max: dayjs().format('YYYY-MM-DD'),
      });

      this.time = input({
        id: 'seetimeTime',
        name: 'time',
        label: 'Hora',
        required: true,
      });

      this.population = input({
        id: 'fish_batch_population',
        name: 'population',
        label: 'Población Inicial',
        placeholder: 'Ingresa la población aquí.',
        require: true,
        min: 0,
        max: 60000,
        step: 1
      });

      this.averageWeight = input({
        id: 'fish_batch_average_weight',
        name: 'averageWeight',
        label: 'Peso promedio inicial [g]',
        placeholder: 'Ingresa el peso aquí.',
        require: true,
        min: 0,
        max: 1000,
        step: 0.1
      });

      this.amount = input({
        id: 'fish_batch_amount',
        name: 'amount',
        label: 'Costo del lote [COP]',
        placeholder: '$0.00',
        require: true,
        min: 100,
        max: 99999999.99
      })
    },
    /**
     * Este metodo se encarga de crear las instancias de los
     * estanque que luego son usadas para sembrar los lotes y
     * luego fltra los estanques que no estan en uso
     * @param {*} data Arreglo cons los datos de los estanques
     */
    __buildFishponds(data) {
      //Se limpia el arreglo
      this.allFishponds = [];
      //Se construye las instancias
      data.forEach(record => {
        this.allFishponds.push({
          id: record.id,
          name: record.name,
          inUse: record.inUse
        });
      });

      //Se filtra por aquellos que no están en uso
      this.fishponds = this.allFishponds.filter(f => !f.inUse);
    },
    /**
     * Reinicia el componente a su estado original
     */
    reset() {
      this.visible = false;
      this.mode = "register";
      this.fishBatch = null;
      //Se resetean los campos
      this.fishpondId.reset();
      this.inThisMoment = true;
      this.population.reset();
      this.averageWeight.reset();
      this.biomass = null;
      this.amount.reset();
    },
    enableForm(mode = 'register', fishBatch = null) {
      this.visible = true;
      this.mode = mode;
      if (mode === 'updating') {
        //Se cargan los datos del lote
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
      this.wire.storeFishBatch(data)
        .then(res => {
          if (res.ok) {
            //Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('fish-batch-created', {fishBatch: res.fishBatch});
            //Se resetea el formulario
            this.reset();
            //Se actualiza el arreglo con los estanques
            this.__buildFishponds(res.fishponds);
          } else {
            if(res.errors?.fishpondInUse){
              this.fishpondId.reset();
              this.__buildFishponds(res.fishponds);
            }else{
              this.notifyErrors(res.errors);
            }
          }
        }).catch(error => {
          console.log(error);
        }).finally(() => {
          this.waiting = false;
        })
    },
    __update() {
      //TODO
    },
    /**
     * Crea un objeto con los datos requeridos por el servidor
     * ya sea para registrar un nuevo lote o para actualizarlo.
     * @returns {*}
     */
    getSubmitData() {
      let data = {
        fishpondId: this.fishpondId.value,
        population: this.population.value,
        averageWeight: this.averageWeight.value,
        amount: this.amount.value,
        inThisMoment: this.inThisMoment,
        setTime: this.setTime,
      }

      if (!this.inThisMoment) {
        data.date = this.date.value;
        if (this.setTime) {
          data.time = this.time.value;
        }
      }

      if (this.mode === 'updating') {
        data.fishBatchId = this.fishBatch?.id;
      }

      return data;
    },
    // *===============================================*
    // *================ VALIDACIONES =================*
    // *===============================================*
    validateFishpond() {
      let value = this.fishpondId.value;
      let ok = false;

      if (value) {
        if (this.fishponds.some(f => f.id === value)) {
          ok = true;
          this.fishpondId.isOk();
        } else {
          this.fishpondId.setError('Este estanque no se encuentra entre los elegibles.');
        }
      } else {
        this.fishpondId.setError('Se debe elegir el estanque donde se va a sembrar');
      }

      return ok;
    },
    /**
     * Se encarga de verificar que la fecha seleccionada por el usuario sea valida
     * y que no supere el momento presente
     * @returns {boolean}
     */
    validateDate() {
      let value = this.date.value;
      let isOk = false;

      //Solo se verifica la fecha si se habilitó para ingresarla
      if (!this.inThisMoment) {
        if (value && value.length > 0) {
          //Se crea una instancia de tiempo para comparar
          let dateSelected = dayjs(value);
          //Se verifica que la fecha sea correcta
          if (dateSelected.isValid()) {
            //Se compara con el momento actual
            let now = dayjs();
            if (dateSelected.isSameOrBefore(now)) {
              this.date.isOk();
              //Se procede a validar la hora
              this.validateTime();
              isOk = true;
            } else {
              this.date.setError('La fecha superior a hoy');
            }
          } else {
            this.date.setError('La fecha es inválida');
          }

        } else {
          this.date.setError('Este campo es requerido');
        }
      } else {
        isOk = true;
      }

      return isOk;
    },
    /**
     * Se encarga de verificar que la hora ingresada por el usuario 
     * sea anterior al momento actual del registro.
     * @returns {boolean}
     */
    validateTime() {
      let value = this.time.value;
      let isOk = false;

      //Solo se verifica si se habilitó para ingresarla
      if (this.setTime) {
        if (value && value.length > 0) {
          /**
         * Solo se valida si la fecha ha sido seleccionada y tiene valor
         * ya que es necesaria para verificar el momento del registro
         */
          if (this.date.value && this.date.value.length > 0 && !this.date.hasError) {
            let dateValue = this.date.value;
            let fullDate = dayjs(`${dateValue} ${value}`);
            let now = dayjs();

            if (fullDate.isValid()) {
              if (fullDate.isSameOrBefore(now)) {
                this.time.isOk();
                isOk = true;
              } else {
                this.time.setError('La combinacion fecha y hora superan al ahora');
              }
            } else {
              this.time.setError('Formato de fecha inválido');
            }
          }
        } else {
          this.time.setError('El campo hora es requerido');
        }
      } else {
        isOk = true;
      }
      return isOk;
    },
    validatePopulation() {
      let value = this.population.value;
      let min = this.population.min;
      let max = this.population.max;

      if (value) {
        if (value > min) {
          if (value < max) {
            this.population.isOk();
            this.updateBiomass();
            this.updateUnitCost();
            return true;
          } else {
            this.population.setError('Debe se mernor que ' + window.formatCurrency(max, 0, 'decimal') + ' peces');
          }
        } else {
          this.population.setError('Debe ser mayor que ' + min);
        }
      } else {
        this.population.setError('Este campo es requerido y no puede ser cero.');
      }

      return false;
    },
    validateAverageWeight() {
      let value = this.averageWeight.value;
      let min = this.averageWeight.min;
      let max = this.averageWeight.max;
      let message = null;

      if (value) {
        if (value > min) {
          if (value < max) {
            this.averageWeight.isOk();
            this.updateBiomass();
            return true;
          } else {
            message = `El peso promedio debe ser menor que ${max} g`;
          }
        } else {
          message = `El peso promedio debe ser mayor o igual que ${min} g`;
        }
      } else {
        message = 'Este campo es requerido y es obligatorio';
      }

      this.averageWeight.setError(message);
      return false;
    },
    /**
     * Verifica que el cmapo del importe sea valido
     */
    validateAmount() {
      let value = this.amount.value;
      let min = this.amount.min;
      let max = this.amount.max;
      let message = null;

      if (value && value > 0) {
        if (value >= min) {
          if (value < max) {
            this.amount.isOk();
            return true;
          } else {
            message = `El costo no puede ser mayor que ${window.formatCurrency(max, 0)}.`;
          }
        } else {
          message = 'El valor minino aceptado es de ' + window.formatCurrency(min, 0);
        }
      } else {
        message = 'El costo del lotes es requerido y debe ser mayor que cero.';
      }

      this.amount.setError(message);
      return false;
    },
    /**
     * Realiza la validación de todos los campos y retorna true si todos
     * son correctos.
     * @returns {boolean}
     */
    validateSubmit() {
      let validations = [];
      validations.push(this.validateFishpond());
      validations.push(this.validateDate());
      validations.push(this.validateTime());
      validations.push(this.validatePopulation());
      validations.push(this.validateAverageWeight());
      validations.push(this.validateAmount());

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
    /**
     * Recupera el valor del importe y se encarga de validarlo
     * y daler formato al campo visible.
     */
    formatAmount(target) {
      let value = window.deleteCurrencyFormat(target.value);
      this.refs.fishBatchAmount.value = window.formatCurrency(value, 0);
      this.amount.value = value;
      this.validateAmount();
      this.updateUnitCost();
    },
    /**
     * Se encarga de calcular la biomasa teniendo en cuenta
     * el peso promedio y la población sembrada. Este metodo se
     * dispara cuando se validan estos dos campos.
     */
    updateBiomass() {
      let population = this.population.value;
      let averageWeight = this.averageWeight.value;
      let biomass = 0;
      let message = null;

      biomass = !this.population.hasError && !this.averageWeight.hasError && population && averageWeight
        ? population * averageWeight
        : 0;

      if (biomass > 0 && biomass < 500) {
        message = `${biomass} g`
      } else if (biomass >= 500) {
        biomass = biomass / 1000;
        message = window.formatCurrency(biomass, 1, 'decimal') + ' Kg';
      }

      this.biomass = message;
    },
    /**
     * Se encarga de actualizar el costo unitario de los
     * alevinos en funcion de la población y el importe
     */
    updateUnitCost() {
      let population = this.population.value;
      let amount = this.amount.value;
      let unitCost = !this.amount.hasError && !this.population.hasError && population && population > 0 && amount
        ? amount / population : null;

      if (unitCost) {
        this.unitCost = window.formatCurrency(unitCost, 2);
      } else {
        this.unitCost = null;
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
  }
}