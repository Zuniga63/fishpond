const _ = require("lodash");
window.round = _.round;

const dayjs = require("dayjs");
require('dayjs/locale/es-do');

//Se adiciona el pluging para tiempo relativo
var relativeTime = require('dayjs/plugin/relativeTime');
dayjs.extend(relativeTime);

var isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

//Se establece en español
dayjs.locale('es-do');

/**
 * Este metodo sirve para poder ordenar instancias segun
 * su fecha.
 * @param {*} item1 Instancia con un objeto tipo dayje
 * @param {*} item2 Instancia con un objeto tipo dayjs
 * @returns int
 */
const sortByDate = (item1, item2) => {
  if (item1.date.isAfter(item2.date)) {
    return 1;
  }

  if (item1.date.isBefore(item2.date)) {
    return -1;
  }

  return 0;

}


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
      // setTimeout(() => {
      //   this.selectFishBatch(this.fishBatchs[0]);
      // }, 1000);
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
        } else if (name === 'new-fish-batch-death') {
          this.dispatch('enable-fish-batch-death-form', info);
        } else if (name === 'update-fish-batch-death') {
          info.mode = 'updating';
          this.dispatch('enable-fish-batch-death-form', info);
        } else if (name === 'new-fish-batch-biometry') {
          this.dispatch('enable-fish-batch-biometry-form', info);
        } else if (name === 'update-fish-batch-biometry') {
          info.mode = 'updating';
          this.dispatch('enable-fish-batch-biometry-form', info);
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
        deathReports: [],
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
        //Variables monetarias
        expenseAmount: 0,                     //Sumatoria de todos los gastos
        totalAmount: data.amount,             //Sumatoria de coste inicial, gastos y alimentación
        unitPrice: 0,                         //Valor unitario de cada pez
        price: 0,                             //El precio de la biomasa en COP/Kg
        //Variables de la biomasa
        initialBiomass: null,                 //Biomasa teniendo encuenta los datos de siembre
        averageWeight: data.initialWeight,    //Peso promedio del la ultima biometría o por defecto peso inicial
        biomass: null,                        //La biomasa teniendo en cuenta el peso promedio
        //Variable relacionadas a la mortalidad
        totalDeaths: 0,
        mortality: 0,
      }

      //Se crean las observaciones
      data.observations.forEach(item => { fishBatch.observations.push(this.createObservation(item)); });

      //Se crean los gastos
      data.expenses.forEach(item => { fishBatch.expenses.push(this.createExpense(item)); });

      //Se crean las biometrias
      data.biometries.forEach(record => { fishBatch.biometries.push(this.createBiometry(record)); })

      //Se crean los registros de las muertes
      data.deaths.forEach(record => { fishBatch.deathReports.push(this.createDeathReport(record)); });

      //Se crean los registros de las dosificaciones
      //TODO

      //Se siembra el lote en el estanque
      let fishpond = this.fishponds.find(item => item.id === data.fishpondId);
      fishpond.seed(data.population);
      fishBatch.fishpond = fishpond;

      this.__updateAllParameters(fishBatch);

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
          ? _.round(Math.PI * Math.pow((data.diameter / 2.0), 2))
          : null;
      }

      //Se calcula el volumen en m3
      if (fishpond.area && data.effectiveHeight || data.maxHeight) {
        let height = data.effectiveHeight || data.maxHeight;
        fishpond.volume = _.round(fishpond.area * height, 1);
        fishpond.depth = height;
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
    createDeathReport(data) {
      return {
        id: data.id,
        deaths: data.deaths,
        totalDeaths: 0,
        initialPopulation: null,
        population: null,
        mortality: null,
        globalMortality: null,
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
        createIsSameUpdate: dayjs(data.createdAt).isSame(dayjs(data.updatedAt)),
        setPopulation(population) {
          this.initialPopulation = population;
          this.population = population - this.deaths;
          this.mortality = _.round((this.deaths / population * 100), 2)
        }
      }
    },
    createBiometry(data) {
      let measurements = data.measurements;
      let sampleSize = 0;
      let totalWeight = 0;
      let averageWeight = 0;
      let totalLong = 0;
      let averageLong = 0;

      measurements.forEach(measuring => {
        sampleSize++;
        totalWeight += measuring.weight ? measuring.weight : 0;
        totalLong += measuring.long ? measuring.long : 0;
      });

      if (sampleSize > 0) {
        averageWeight = totalWeight / sampleSize;
        averageLong = _.round(totalLong / sampleSize, 2);
      }

      sampleSize = sampleSize || null;
      totalWeight = totalWeight || null;
      averageWeight = averageWeight || null;
      totalLong = totalLong || null;
      averageLong = averageLong || null;

      return {
        id: data.id,
        date: dayjs(data.date),
        measurements: data.measurements,
        sampleSize,
        samplePercentage: null,
        totalWeight,
        averageWeight,
        totalLong,
        averageLong,
        population: null,
        biomass: { value: null, unit: 'g' },
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
        createIsSameUpdate: dayjs(data.createdAt).isSame(dayjs(data.updatedAt)),
        setPopulation(population) {
          this.population = population;
          this.biomass.value = population * this.averageWeight;
          if (this.biomass.value >= 1000) {
            this.biomass.value = this.biomass.value / 1000;
            this.biomass.unit = 'Kg.'
          }
          this.samplePercentage = _.round((this.sampleSize / population) * 100, 1);
        }
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
    addDeathReport(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Se crea el reporte de muertes
      let report = this.createDeathReport(detail.death);
      //Se guarda el reporte
      fishBatch.deathReports.push(report);
      //Se diminuye la población
      fishBatch.population -= report.deaths;
      //Se actualizna los parametros
      this.__updateAllParameters(fishBatch, true);

      this.formActive = false;
    },
    updateDeathReport(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Se crea el reporte de muertes
      let report = this.createDeathReport(detail.death);
      //Recupero el reporte original
      let original = fishBatch.deathReports.find(item => item.id === report.id);

      //Se actualizan las poblaciones
      fishBatch.population += original.deaths - report.deaths;

      //Se actualiza el reporte 
      for (const key in report) {
        if (Object.hasOwnProperty.call(original, key)) {
          original[key] = report[key];
        }
      }

      //Se actualizna los parametros
      this.__updateAllParameters(fishBatch, true);

      this.formActive = false;
    },
    removeDeathReport(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Actualizo la población
      fishBatch.population += detail.report.deaths;
      //Se recupera el indice del reporte
      let index = fishBatch.deathReports.findIndex(item => item.id === detail.report.id);

      if (index >= 0) {
        //Se elimina la instancia
        fishBatch.deathReports.splice(index, 1);
        this.__updateAllParameters(fishBatch, true);
      }
    },
    addBiometry(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Se crea la instancia de la biometría
      let biometry = this.createBiometry(detail.biometry);
      //Se adiciona al listado
      fishBatch.biometries.push(biometry);
      //Se ordena el arreglo
      fishBatch.biometries.sort(sortByDate);
      //Se actualizna los parametros
      this.__updateAllParameters(fishBatch, true);

      this.formActive = false;
    },
    updateBiometry(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Se crea la instancia de la biometría
      let biometry = this.createBiometry(detail.biometry);
      //recupero la original
      let original = fishBatch.biometries.find(item => item.id === biometry.id);
      //Se actualiza la original
      for (const key in biometry) {
        if (Object.hasOwnProperty.call(original, key)) {
          original[key] = biometry[key];
        }
      }

      //Se ordena el arreglo
      fishBatch.biometries.sort(sortByDate);

      //Se actualizna los parametros
      this.__updateAllParameters(fishBatch, true);

      this.formActive = false;
    },
    removeBiometry(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(item => item.id === detail.fishBatch.id);
      //Recupero el indice de la biometría
      let index = fishBatch.biometries.findIndex(item => item.id === detail.biometry.id);

      if (index >= 0) {
        //Se elimina la instancia
        fishBatch.biometries.splice(index, 1);
        this.__updateAllParameters(fishBatch, true);
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
      let weight = fishBatch.biomass ? fishBatch.averageWeight * fishBatch.population : null;
      let price = null;

      if (weight) {
        weight = weight / 1000;
        price = _.round(totalAmount / weight, 0);
      }

      //Se actualiza el objeto
      fishBatch.expenseAmount = expenses;
      fishBatch.totalAmount = totalAmount;
      fishBatch.unitPrice = unitPrice;
      fishBatch.price = price;
    },
    /**
     * Se encarga de actualizar los parametros referentes a la
     * biomasa del lote y su evolución con respecto a todas las biometrías
     * realizadas en campo. ademas completa los reportes de muertes y las biometrías.
     * @param {*} fishBatch Instancia del lote a actualizar
     */
    __updateBiomassParameters(fishBatch) {
      let initialBiomass = { value: null, unit: 'g.' }
      let biomass = { value: null, unit: 'g.' }
      let averageWeight = fishBatch.averageWeight;

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

      //Se actualizan las

    },
    /**
     * Se encarga de completar o actualizar los reportes de las muertes agregando
     * el valor de la población a cada instnacia y de recuperar de paso 
     * el ultimo valor medido del peso promedio.
     * @param {*} fishBatch Instancia del lote a actualizar
     */
    __updateBiometriesAndDeaths(fishBatch) {
      let population = fishBatch.initialPopulation;
      let totalDeaths = 0;
      let mortality = 0;
      let averageWeight = fishBatch.initialWeight;
      let indexBiometry = 0;
      let biometry = null;

      /** 
       * Se recorren todas las muertes para ir actualizando el numero de la población
       * y mientras esto ocurre se van actualizando todas las biometrías que sean anterior a 
       * los reportes de muertes.
       */
      fishBatch.deathReports.forEach(report => {
        //Se actualizan variables globales
        totalDeaths += report.deaths;
        mortality = _.round((totalDeaths / fishBatch.initialPopulation) * 100, 2);
        //Se actualiza el reporte
        report.setPopulation(population);
        report.globalMortality = mortality;
        report.totalDeaths = totalDeaths;

        if (indexBiometry < fishBatch.biometries.length) {
          biometry = fishBatch.biometries[indexBiometry];
          while (biometry.date.isBefore(report.createdAt)) {
            //Se actualizan los parametros de la biometría
            biometry.setPopulation(population);
            //Se recupera el peso promedio
            averageWeight = biometry.averageWeight;
            //Se incrementa el index
            indexBiometry++;
            //Se verifica nuevmente que existan datos
            if (indexBiometry < fishBatch.biometries.length) {
              //Se refresca la istancia
              biometry = fishBatch.biometries[indexBiometry];
            } else {
              break;
            }
          }//.end while
        }//.end if

        //Se disminuye la población
        population -= report.deaths;
      });

      //Finalmente se actualizan las biometrías falstantes
      for (let index = indexBiometry; index < fishBatch.biometries.length; index++) {
        let biometry = fishBatch.biometries[index];
        //Se actualizan los parametros de la biometría
        biometry.setPopulation(population);
        //Se recupera el peso promedio
        averageWeight = biometry.averageWeight;
      }

      fishBatch.averageWeight = averageWeight;
    },
    __updateAllParameters(fishBatch, emitEvent = false) {
      this.__updateBiometriesAndDeaths(fishBatch);
      this.__updateBiomassParameters(fishBatch);
      this.__updateMonetaryParameters(fishBatch);

      if (emitEvent) {
        this.dispatch('fish-batch-was-updated')
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
require('./fish-batch-death-form');
require('./fish-batch-biometry-form');