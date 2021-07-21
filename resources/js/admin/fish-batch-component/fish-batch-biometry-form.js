import { isEmpty, isNumber } from 'lodash';
import input from '../input';
const dayjs = require('dayjs');
var isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

var isSameOrAfter = require('dayjs/plugin/isSameOrAfter');
dayjs.extend(isSameOrAfter);

window.fishBatchBiometryForm = () => {
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
    /** Instancia original de la biometría a modificar */
    originalBiometry: null,
    // *===========================================*
    // *========== Campos del formulario ==========*
    // *===========================================*
    /** Establece el momento en el que se registra el gasto */
    inThisMoment: true,
    /** Fecha del gasto en formato YYYY-MM-DD */
    date: null,
    /** Habilita el ingreso manual de la hora */
    setTime: false,
    /** Hora en la que se registra el gasto */
    time: null,
    /** Listado con las mediciones a guardar */
    measurements: [],
    samplePercentage: 0,
    /** Peso del pez */
    fishWeight: null,
    /** logitud del pez */
    fishLong: null,
    resume: {
      population: 0,
      sampleSize: 0,
      totalWeight: 0,
      averageWeight: 0,
      totalLong: 0,
      averageLong: 0,
      samplePercentage: 0,
      reset() {
        this.sampleSize = 0;
        this.totalWeight = 0;
        this.averageWeight = 0;
        this.totalLong = 0;
        this.averageLong = 0;
        this.samplePercentage = 0;
      },
      setSampleSize(population, sample) {
        this.population = population,
          this.sampleSize = sample;
        if (isNumber(population) && isNumber(sample) && population > 0 && population >= sample) {
          this.samplePercentage = (sample / population) * 100;
        }
      },
      addWeight(value) {
        this.totalWeight += value;
        this.averageWeight = this.sampleSize ? this.totalWeight / this.sampleSize : 0;
      },
      addLong(value) {
        this.totalLong += value;
        this.averageLong = this.sampleSize ? this.totalLong / this.sampleSize : 0;
      }
    },
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
      //FECHA
      this.date = input({
        id: 'biometryDate',
        name: 'date',
        label: 'Selecciona una fecha',
        required: true,
        max: dayjs(),
        min: dayjs(),
      });

      //HORA
      this.time = input({
        id: 'biometryTime',
        name: 'time',
        label: 'Hora',
        required: true,
        value: dayjs().format('HH:mm')
      });

      //WEIGHT
      this.fishWeight = input({
        id: 'biometryWeight',
        name: 'weight',
        label: 'Peso [g]',
        placeholder: 'Peso en gramos.',
        min: 0,
        max: 1000,
        step: 0.1
      });

      //LONG
      this.fishLong = input({
        id: 'biometryLong',
        name: 'long',
        label: 'Logitud [cm]',
        placeholder: 'Largo en cm.',
        min: 0,
        max: 100,
        step: 0.1
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
      this.inThisMoment = true;
      this.setTime = false;
      this.fishWeight.reset();
      this.fishLong.reset();
      this.resume.reset();
      this.measurements = [];
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
      this.date.min = dayjs(this.fishBatch.seedtime);

      if (detail.mode === 'updating') {
        this.originalBiometry = detail.data;
        this.originalBiometry.measurements.forEach(item => {
          this.measurements.push(item);
        });

        this.inThisMoment = false;
        this.date.value = detail.data.date.format('YYYY-MM-DD');
        this.setTime = true;
        this.time.value = detail.data.date.format('HH:mm');

        this.updateStatisticts();
        //TODO: algoritmo para montar los datos al componente
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
    addMeasuring() {
      if (this.validateMeasuring()) {
        this.measurements.push({
          weight: this.fishWeight.value,
          long: this.fishLong.value
        });


        this.fishWeight.reset();
        this.fishLong.reset();
        this.updateStatisticts();
      }
    },
    /**
     * Retira la medición del arreglo de mediciones
     * @param {*} index Ubicación a remover del listado
     */
    removeMeasuring(index) {
      if (isNumber(index) && index >= 0 && index < this.measurements.length) {
        this.measurements.splice(index, 1);
        this.updateStatisticts();
      }
    },
    updateStatisticts() {
      this.resume.reset();
      this.resume.setSampleSize(this.fishBatch.population, this.measurements.length);

      this.measurements.forEach(measuring => {
        this.resume.addWeight(measuring.weight);
        if (isNumber(measuring.long)) {
          this.resume.addLong(measuring.long);
        }
      });
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
      this.wire.storeBiometry(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              biometry: res.biometry,
            }
            // Se emite el evento con los datos del nuevo lote de peces
            this.dispatch('biometry-was-stored', info);
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
      this.wire.updateBiometry(data)
        .then(res => {
          if (res.ok) {
            let info = {
              fishBatch: this.fishBatch,
              biometry: res.biometry,
            }
            // Se emite el evento
            this.dispatch('biometry-was-updated', info);
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
      let date = this.date.value;
      let time = this.time.value;
      let data = {
        fishBatchId: this.fishBatch.id,
        inThisMoment: this.inThisMoment,
        setTime: this.setTime,
        measurements: this.measurements
      };

      if (!this.inThisMoment) {
        data.date = date;
        if (this.setTime) {
          data.time = time;
          data.fullDate = `${date} ${time}`;
        }
      }

      if (this.mode === 'updating') {
        data.biometryId = this.originalBiometry.id;
      }

      return data;
    },
    // *===============================================*
    // *================ VALIDACIONES =================*
    // *===============================================*
    validateDate() {
      let value = this.date.value;
      let min = dayjs(this.date.min).startOf('day');
      let max = dayjs();
      let errorMessage = null;

      if (!this.inThisMoment) {
        if (!isEmpty(value)) {
          let date = dayjs(value, 'YYYY-MM-DD').startOf('day');
          if (date.isSameOrAfter(min)) {
            if (date.isSameOrBefore(max)) {
              this.date.isOk();
              //Se valida la hora
              if (this.setTime) {
                this.validateTime();
              }

              return true;
            } else {
              errorMessage = "No se pueden agregar gastos en el futuro.";
            }
          } else {
            errorMessage = "La fecha debe ser mayor a la fecha del lote";
          }
        } else {
          errorMessage = "Se debe elegir una fecha valida";
        }
      } else {
        return true;
      }

      this.date.setError(errorMessage);
      return false;
    },
    validateTime() {
      let time = this.time.value;
      let date = this.date.value;
      let min = dayjs(this.fishBatch.seedtime);
      let max = dayjs();
      let errorMessage = null;

      if (!this.inThisMoment && this.setTime && !this.date.hasError) {
        if (!isEmpty(time)) {
          let fullDate = dayjs(`${date} ${time}`, 'YYYY-MM-DD HH:mm');
          if (fullDate.isSameOrAfter(min)) {
            if (fullDate.isSameOrBefore(max)) {
              this.time.isOk();
              return true;
            } else {
              errorMessage = "Los gastos no se pueden realizar en el futuro";
            }
          } else {
            errorMessage = "Se intenta registrar un gasto anterior al lote.";
          }
        } else {
          errorMessage = "Debe escribir o seleccionar una hora válida";
        }
      } else {
        return true;
      }

      this.time.setError(errorMessage);
      return false;
    },
    validateMeasuring() {
      let weight = this.fishWeight.value;
      let weightMin = this.fishWeight.min;
      let weightMax = this.fishWeight.max;
      let weightMessage = null;

      let long = this.fishLong.value;
      let longMin = this.fishLong.min;
      let longMax = this.fishLong.max;

      //Validación opcional del lago del pez
      if (long || isNumber(long)) {
        if (long > longMin) {
          if (long < longMax) {
            this.fishLong.isOk();
          } else {
            this.fishLong.setError(`Debe ser menor que ${longMax} cm.`);
          }
        } else {
          this.fishLong.setError(`Debe ser mayor que ${longMin}`);
        }
      } else {
        this.fishLong.isOk();
      }

      if (weight || isNumber(weight)) {
        if (weight > weightMin) {
          if (weight < weightMax) {
            this.fishWeight.isOk();
            return true;
          } else {
            weightMessage = `Debe ser menor que ${weightMax} g.`;
          }
        } else {
          weightMessage = `Debe ser superior a ${weightMin} g.`;
        }
      } else {
        weightMessage = 'Es obligatorio.';
      }

      this.fishWeight.setError(weightMessage);
      return false;
    },
    /**
     * Realiza todas las validaciones del formulario
     * y returna true si todas fueron correctas.
     * @returns bool
     */
    validateSubmit() {
      let validations = [];
      validations.push(this.validateDate());
      validations.push(this.validateTime());
      validations.push(this.measurements.length > 0);

      //Retorna false si alguna de las validaciones es falsa, pero valida todos los campos
      return !validations.some(val => val === false);
    }
  }
}