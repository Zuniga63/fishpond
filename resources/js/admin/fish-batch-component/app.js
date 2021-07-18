const _ = require("lodash");

const dayjs = require("dayjs");
require('dayjs/locale/es-do');

//Se adiciona el pluging para tiempo relativo
let relativeTime = require('dayjs/plugin/relativeTime');
dayjs.extend(relativeTime);

let isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

//Se establece en español
dayjs.locale('es-do');


window.app = () => {
  return {
    tab: 'sown-lot',    //[sown-lot, harvested-batch]
    /** Define si la vista es del home o de algun lote */
    home: true,
    /** Arreglo con todos los lotes de peces */
    allFishBatchs: [],
    /** Arreglo con los estanques que están almacenados */
    fishponds: [],
    /** Arreglo con los lotes de peces correspodientes al tab */
    fishBatchs: [],
    /** Respondable de mostrar los formularios en pantalla */
    formActive: false,
    /** Se encarga de las peticones al servidor */
    wire: null,
    /** Se encarga de administrar los eventos personalizados */
    dispatch: null,
    /** Permite acceder a las referencias del componente en el DOM */
    refs: null,
    // *===========================================================*
    // *================= METODOS DEL COMPONENTE ==================*
    // *===========================================================*
    init(wire = null, dispatch = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      let data = window.initialData;

      //Se crean los estanques
      data.fishponds.forEach(record => {
        let fishpond = this.createFishpond(record);
        this.fishponds.push(fishpond);
      });

      //Se crean los lotes de peces
      data.fishBatchs.forEach(record => {
        let fishBatch = this.createFishBatch(record);
        this.allFishBatchs.push(fishBatch);
      });

      //Se seleccionan los lotes que están sembrados
      this.updateFishBatchList();
      setTimeout(() => {
        this.selectFishBatch(this.fishBatchs[0]);
      }, 1000);
    },
    /**
         * Cambia el valor de la varible tab y actualiza el listado de
         * lotes que se muestran en el panel principal.
         * @param {string} tab En nombre del tab por el que se va a cambiar
         */
    changeTab(tab = 'sown-lot') {
      if (tab !== this.tab) {
        this.tab = tab;
        this.updateFishBatchList();
      }
      //Metodo para actualizar el listado de lotes
    },
    updateFishBatchList() {
      let tab = this.tab;
      //Se recuperan los lotes segun el tab
      if (tab === 'sown-lot') {
        this.fishBatchs = this.allFishBatchs.filter(b => b.harvest === null);
      } else if (tab === 'harvested-batch') {
        this.fishBatchs = this.allFishBatchs.filter(b => b.harvest !== null);
      }

      //Algoritmo para ordenar por la fecha de siembra
    },
    enableForm(name = null, fishBatch = null, data = null) {
      if (name) {
        this.formActive = true;
        let info = {
          mode: 'register',
          fishBatch: fishBatch,
          data: data,
        }

        if (name === 'new-fish-batch') {
          this.dispatch('enable-fish-batch-form', info);
        } else if (name === 'update-fish-batch') {
          info.mode = "updating";
          this.dispatch('enable-fish-batch-form', info);
        } else if (name === 'new-fish-batch-observation') {
          this.dispatch('enable-fish-batch-observation-form', info);
        } else if (name === 'update-fish-batch-observation') {
          info.mode = 'updating';
          this.dispatch('enable-fish-batch-observation-form', info);
        } else if (name === "new-fish-batch-expense") {
          this.dispatch('enable-fish-batch-expense-form', info);
        } else if (name === 'update-fish-batch-expense') {
          info.mode = 'updating';
          this.dispatch('enable-fish-batch-expense-form', info);
        }
        else {
          this.formActive = false;
        }
      }
    },
    selectFishBatch(fishBatch) {
      this.home = false;
      this.dispatch('fish-batch-selected', fishBatch);
    },
    // *===========================================================*
    // *================= CREACIÓN DE INSTANCIAS ==================*
    // *===========================================================*
    /**
     * Recibe los datos puros del servidor y los convierte en instancias de 
     * lotes. Este metodo se debe llamar despues de haber creado los estanqes
     * @param {*} data Datos puros de lotes
     */
    createFishBatch(data) {
      //Se crea el objeto con la estructura de datos
      let fishBatch = {
        //Datos provenientes del servidor
        id: data.id,
        seedtime: dayjs(data.seedtime),
        harvest: data.harvest ? dayjs(data.harvest) : null,
        initialPopulation: data.initialPopulation,
        initialWeight: data.initialWeight,
        population: data.population,
        amount: data.amount,
        observations: [],
        expenses: [],
        biometries: [],
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
        //Variables monetarias
        expenseAmount: 0,                     //Sumatoria de todos los gastos
        totalAmount: data.amount,             //Sumatoria de coste inicial, gastos y alimentación
        unitPrice: 0,                         //Valor unitario de cada pez
        //Variables de la biomasa
        initialBiomass: null,                 //Biomasa teniendo encuenta los datos de siembre
        averageWeight: data.initialWeight,    //Peso promedio del la ultima biometría o por defecto peso inicial
        biomass: null,                        //La biomasa teniendo en cuenta el peso promedio
      }

      //Se crean las observaciones
      data.observations.forEach(item => { fishBatch.observations.push(this.createObservation(item)); });

      //Se crean los gastos
      data.expenses.forEach(item => { fishBatch.expenses.push(this.createExpense(item)); });

      //Se crean las biometrias
      //TODO

      //Se crean los registros de las muertes
      //TODO

      //Se crean los registros de las dosificaciones
      //TODO

      //Se siembra el lote en el estanque
      let fishpond = this.fishponds.find(item => item.id === data.fishpondId);
      fishpond.seed(data.population);
      fishBatch.fishpond = fishpond;

      //Se actualiza la biomasa
      this.__updateBiomassParameters(fishBatch);
      //Se actualizan los parametros monetarios
      this.__updateMonetaryParameters(fishBatch);

      return fishBatch;
    },
    /**
     * Recibe los datos puros del servidor y los convierte en una instancia
     * de estanque para que pueda ser usado con un lote.
     * @param {*} data Objeto con los datos generales de un estanque
     */
    createFishpond(data) {
      let fishpond = {};
      const FISHPOND_TYPES = {
        rectangular: 'Rectangular',
        circular: 'Circular',
      }

      //Se guarda la información basica
      fishpond.id = data.id;
      fishpond.name = data.name;
      fishpond.type = FISHPOND_TYPES[data.type];
      fishpond.capacity = data.capacity;
      fishpond.inUse = data.inUse;

      //Se calcula el area en m2
      if (data.type === 'rectangular') {
        fishpond.area = data.width && data.long
          ? _.round(data.width * data.long, 2)
          : null;
      } else if (data.type === 'circular') {
        fishpond.area = data.diameter
          ? _.round(Math.PI * Math.pow((data.diameter / 2.0), 1))
          : null;
      }

      //Se calcula el volumen en m3
      if (fishpond.area && data.effectiveHeight || data.maxHeight) {
        let height = data.effectiveHeight || data.maxHeight;
        fishpond.volume = _.round(fishpond.area * height, 1);
      }

      //Se agregan las variable situacionales
      fishpond.population = null;
      fishpond.densityByArea = null;
      fishpond.desityByVolume = null;

      //Se agrea un metodo para sembrar una poblacion
      //que se encarga de actualizar las variables situacionales
      fishpond.seed = function (population = null) {
        if (population && population > 0) {
          this.population = population;
          this.densityByArea = this.area ? _.round(population / this.area, 0) : null;
          this.densityByVolume = this.volume ? _.round(population / this.volume, 0) : null;
        }
      }

      return fishpond;
    },
    createObservation(data) {
      return {
        id: data.id,
        title: data.title,
        message: data.message,
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
        createIsSameUpdate: dayjs(data.createdAt).isSame(dayjs(data.updatedAt)),
      };
    },
    createExpense(data) {
      return {
        id: data.id,
        date: dayjs(data.date),
        description: data.description,
        amount: data.amount,
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
        createIsSameUpdate: dayjs(data.createdAt).isSame(dayjs(data.updatedAt)),
      }
    },
    // *===============================================*
    // *================= MUTACIONES ==================*
    // *===============================================*
    /**
     * Se encarga de crear las instancias de los lotes y agregarlos en el arreglo principal
     * @param {*} data Objetos con los datos de un lote de peces
     */
    addNewFishBatch(data) {
      console.log(data);
      //Se crea la instancia
      let fishBatch = this.createFishBatch(data);
      //Se agrega al arreglo principal
      this.allFishBatchs.push(fishBatch);
      //Se actualiza el listado
      this.updateFishBatchList();
      this.formActive = false;
    },
    updateFishBatch(data) {
      //Se crea una nueva instancia
      let fishBatchUpdated = this.createFishBatch(data);
      //Se recupera la antigua instnacia
      let lastFishBatch = this.allFishBatchs.find(item => item.id === fishBatchUpdated.id);
      //Se actualizan todos los campos
      for (const key in fishBatchUpdated) {
        if (Object.hasOwnProperty.call(fishBatchUpdated, key)) {
          lastFishBatch[key] = fishBatchUpdated[key];
        }
      }

      //Se actualiza el listado
      this.updateFishBatchList();
      this.formActive = false;
    },
    addObservation(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(batch => batch.id === detail.fishBatch.id);
      //Se crea la observación
      let observation = this.createObservation(detail.observation);
      //Se agrega al listado
      fishBatch.observations.push(observation);
      this.dispatch('observation-was-added');
      //Se deshabilita el formulario
      this.formActive = false;
    },
    updateObservation(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(batch => batch.id === detail.fishBatch.id);
      //Se crea la observación
      let observation = this.createObservation(detail.observation);
      //Se busca la observación original
      let original = fishBatch.observations.find(item => item.id === observation.id);
      //Se actualizan los campos
      for (const key in observation) {
        if (Object.hasOwnProperty.call(original, key)) {
          original[key] = observation[key];
        }
      }

      //Se deshabilita el formulario
      this.formActive = false;
    },
    addExpense(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Se crea la instancia del gasto
      let expense = this.createExpense(detail.expense);
      //Se agrega al listado
      fishBatch.expenses.push(expense);
      //Se actualiza los importes
      this.__updateMonetaryParameters(fishBatch);
      this.dispatch('expense-was-added');
      //Se deshabilita el formulario
      this.formActive = false;
    },
    updateExpense(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Se crea la instancia del gasto
      let expense = this.createExpense(detail.expense);
      //Se recupera el gasto original
      let original = fishBatch.expenses.find(item => item.id === expense.id);
      //Se actualiza el original
      for (const key in expense) {
        if (Object.hasOwnProperty.call(original, key)) {
          original[key] = expense[key];
        }
      }

      //Se actualizan los parametros monetarios
      this.__updateMonetaryParameters(fishBatch);
      //Se emite el evento de que el gasto fue agregado
      this.dispatch('expense-was-added');
      //Se deshabilita el formulario
      this.formActive = false;
    },
    removeExpense(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Se recupera el indice del gasto
      let index = fishBatch.expenses.findIndex(item => item.id === detail.expense.id);
      //Se elimina el estanque del arreglo
      if (index >= 0) {
        //Se elimina la instancia
        fishBatch.expenses.splice(index, 1);
        this.__updateMonetaryParameters(fishBatch);
        this.dispatch('expense-was-removed');
      }
    },
    // *===============================================*
    // *================= UTILIDADES ==================*
    // *===============================================*
    /**
     * Se encarga de actualizar los parametros monetarios del lote
     * teniendo en cuanta el costo inicial de los alevinos, todos 
     * los gastos financieros y el valor de las dosificaciones.
     * @param {*} fishBatch Instancia del lote a actualizar
     */
    __updateMonetaryParameters(fishBatch) {
      let initialCost = fishBatch.amount;
      let expenses = fishBatch.expenses.reduce((amount, expense) => amount + expense.amount, 0);
      let totalAmount = initialCost + expenses;
      let unitPrice = _.round(totalAmount / fishBatch.population, 0);

      //Se actualiza el objeto
      fishBatch.expenseAmount = expenses;
      fishBatch.totalAmount = totalAmount;
      fishBatch.unitPrice = unitPrice;
    },
    /**
     * Se encarga de actualizar los parametros referentes a la
     * biomasa del lote y su evolución con respecto a todas las biometrías
     * realizadas en campo.
     * @param {*} fishBatch Instancia del lote a actualizar
     */
    __updateBiomassParameters(fishBatch) {
      let initialBiomass = { value: null, unit: 'g.' }
      let biomass = { value: null, unit: 'g.' }
      let averageWeight = fishBatch.initialWeight;
      //Metodo para recupera el ultimo peso promedio

      initialBiomass.value = fishBatch.initialPopulation * fishBatch.initialWeight;
      if (initialBiomass.value >= 1000) {
        initialBiomass.value = _.round(initialBiomass.value / 1000, 2);
        initialBiomass.unit = 'Kg.'
      }

      biomass.value = averageWeight * fishBatch.population;
      if (biomass.value >= 1000) {
        biomass.value = _.round(biomass.value / 1000, 2);
        biomass.unit = 'Kg.'
      }

      fishBatch.averageWeight = averageWeight;
      fishBatch.initialBiomass = initialBiomass;
      fishBatch.biomass = biomass;

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
          let value = data[key] ? data[key].toString() : 'null';
          let keyLength = key.length;
          let valueLength = value.length;
          let line = `${key}: ${value}`;
          line += ' '.repeat(bodyLength - line.length);
          text += `| ${line} |\n`;
        }//end if
      }//end for
      text += header;
      console.log(text, data);
    },
  }
}

require('./fish-batch-form');
require('./fish-batch-component');
require('./fish-batch-observation-form');
require('./fish-batch-expense-form');