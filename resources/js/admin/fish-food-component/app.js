import './fish-food-form';
import './fish-food-stock-form';

//DAYJS
import dayjs from 'dayjs';
import 'dayjs/locale/es-do';
import { round } from 'lodash';
dayjs.locale('es-do');
var relativeTime = require('dayjs/plugin/relativeTime');
dayjs.extend(relativeTime);

window.dayjs = dayjs;
window.round = round;

window.app = () => {
  return {
    tab: 'foods',
    /**
     * Listado con todos los alimentos registrados
     * por el susuario.
     */
    fishFoodList: [],
    /**
     * Identificador del alimento para seleccionar los stocks
     */
    fishFoodId: null,
    /**
     * Stocks del alimento seleccionado
     */
    stocks: [],
    /**
     * Objeto con las etapas que los alimentos pueden tener
     */
    stages: null,
    /** Habilita y deshailita los formularios */
    formActive: false,

    //*========================================*
    //*============= CONTROLADES =============*
    //*========================================*
    wire: null,
    dispatch: null,
    refs: null,
    //*=========================================*
    //*============= CONSTRUCTORES =============*
    //*=========================================*
    init(wire = null, dispatch = null, refs = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;

      //Se establecen las etapas
      this.stages = window.initialData.stages;
      //Se crean las instancias de los alimentos
      window.initialData.fishFoodList.forEach(record => { this.fishFoodList.push(this.createdFishFood(record)); });
    },
    createdFishFood(data) {
      //Se construyen los stocks
      let stocks = [];
      let totalStock = 0;
      let totalAmount = 0;
      let unitValue = 0;

      data.stocks.forEach(record => {
        stocks.push(this.createStocks(record))
        totalStock += record.stock;
        totalAmount += record.stock * (record.amount / record.initialStock);
      });

      // Valor unitario en [COP]/g
      unitValue = totalStock ? totalAmount / totalStock : 0;

      let stock = Stock(totalStock, unitValue);

      //Se crean las variables de auditoría
      let createdAt = dayjs(data.createdAt);
      let updatedAt = dayjs(data.updatedAt);
      let createIsSameUpdate = createdAt.isSame(updatedAt);

      //se crea el objeto
      let fishFood = {
        id: data.id,
        name: data.name,
        brand: data.brand,
        stage: data.stage,
        stocks,
        stock,
        dosed: 0,
        outOfStock: null,
        createdAt,
        updatedAt,
        createIsSameUpdate,
      }

      window.fishFood = fishFood;
      return fishFood;
    },
    /**
     * Crea una instancia con los datos del stock del alimento
     * @param {*} data Datos del stock a crear
     * @returns 
     */
    createStocks(data) {
      //Se crean las variables de auditoría
      let createdAt = dayjs(data.createdAt);
      let updatedAt = dayjs(data.updatedAt);
      let createIsSameUpdate = createdAt.isSame(updatedAt);
      //Se calcula el valor unitario
      let unitValue = data.amount / data.initialStock;

      let stock = {
        id: data.id,
        initialStock: Stock(data.initialStock, unitValue, 'g'),
        stock: Stock(data.stock, unitValue, 'g'),
        createdAt,
        updatedAt,
        createIsSameUpdate
      }

      return stock;
    },
    updateFishFoodStock(fishFood){
      let totalStock = 0;
      let amount = 0;

      fishFood.stocks.forEach(item => {
        totalStock += item.stock.absoluteQuantity;
        amount += item.stock.getAmount();
      })

      fishFood.stock.setQuantity(totalStock);
      fishFood.stock.unitValue = totalStock > 0 ? amount / totalStock : 0;
    },
    /**
     * Actualiza el listado de stocks cuando un alimento
     * es seleccionado en la la vista.
     */
    fishFoodChange() {
      this.stocks = [];
      //Se busca el concentrado
      let food = this.fishFoodList.find(food => food.id === this.fishFoodId);
      if(food){
        this.stocks = food.stocks;
      }
    },
    enableForm() {
      if (this.tab === 'foods') {
        this.dispatch('enable-fish-food-form', { mode: 'register' });
        this.formActive = true;
      } else if (this.tab === 'inventory') {
        this.dispatch('enable-stock-form', {
          mode: 'register',
          fishFoodId: this.fishFoodId
        });
        this.formActive = true;
      }
    },
    editFood(fishFood) {
      this.dispatch('enable-fish-food-form', {
        mode: 'updating',
        fishFood
      });

      this.formActive = true;
    },
    addFishFood(data) {
      //Se crea la instancia
      let fishFood = this.createdFishFood(data);
      //Se adiciona al listado
      this.fishFoodList.push(fishFood);
      this.formActive = false;
    },
    updateFishFood(data) {
      //Se crea la instancia
      let fishFood = this.createdFishFood(data);
      //Se recupera la original
      let original = this.fishFoodList.find(item => item.id === fishFood.id);
      //Se actualiza el original
      for (const key in fishFood) {
        if (Object.hasOwnProperty.call(original, key)) {
          original[key] = fishFood[key];
        }
      }

      this.formActive = false;
    },
    destroyFishFood(id, index) {
      window.Swal.fire({
        title: "¿Desea eliminar este alimento?",
        text: "Tenga en cuenta que no se pueden eliminar alimentos que tengan inventarios o dosificaciones.",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyFishFood(id).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.ok) {
            //Se elimina la instancia
            this.fishFoodList.splice(index, 1);
            this.fishFoodId = null;
            this.fishFoodChange();
          } else {
            console.log(result.value.errors);
          }
        }
      });
    },
    addFishFoodStock(data){
      //Recupero el alimento
      let fishFood = this.fishFoodList.find(item => item.id === data.fishFoodId);
      //Se crea el stock
      let stock = this.createStocks(data.stock);
      //Se adiciona al alimento
      fishFood.stocks.push(stock);
      //Se actualiza el alimento
      this.updateFishFoodStock(fishFood);
      //Se actualiza el listado de inventario
      this.fishFoodChange();
      this.formActive = false;
    },
    destroyFishFoodStock(id) {
      window.Swal.fire({
        title: "¿Desea eliminar este inventario?",
        text: "Tenga en cuenta que no se pueden eliminar inventarios que se han dosificado.",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyFishFoodStock(id).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.ok) {
            //Se recupera el alimento que tiene el stock
            let fishFood = this.fishFoodList.find(item => item.id === this.fishFoodId);
            if (fishFood) {
              //Se recupera el index del stock a eliminar
              let index = fishFood.stocks.findIndex(item => item.id === id);
              if (index >= 0) {
                //Se retira el stock
                fishFood.stocks.splice(index, 1);

                //Se actualiza el stock
                this.updateFishFoodStock(fishFood);
                this.fishFoodChange();
              }
            }
          } else {
            console.log(result.value.errors);
          }
        }
      });
    }
  }
}

/**
 * 
 * @param {int} quantity Cantidad del stock preferiblemente en g.
 * @param {float} unitValue Valor unitario en pesos por gramo
 * @param {string} unit  Unidad de medida 
 * @returns 
 */
const Stock = (quantity = 0, unitValue = 0, unit = 'g') => {
  let absoluteQuantity = quantity;
  if (quantity >= 1e3 && quantity < 1e6) {
    quantity /= 1e3;
    unit = 'Kg';
  } else if (quantity >= 1e6) {
    quantity /= 1e6;
    unit = 'Ton'
  };

  return {
    /** Es la cantidad en gramos, unidad basica */
    absoluteQuantity,
    /** Es la cantidad segun la unidad de medida */
    quantity,
    /** Unidad de masa, g, Kg, Ton */
    unit,
    /** Valor Unitario en g */
    unitValue,
    setQuantity(quantity, unit = 'g') {
      //Se calcula la cantidad absoluta
      if (unit === 'g') {
        this.absoluteQuantity = quantity;
      } else if (unit === 'Kg') {
        this.absoluteQuantity = quantity / 1e3;
      } else if (unit === 'Ton') {
        this.absoluteQuantity = quantity / 1e6;
      }

      //Ahora se convierte a quantity
      if (this.absoluteQuantity >= 1e3) {
        this.quantity = this.absoluteQuantity / 1e3;
        this.unit = 'Kg'
      } else if (this.absoluteQuantity >= 1e6) {
        this.quantity = this.absoluteQuantity / 1e6;
        this.unit = 'Ton'
      } else {
        this.quantity = this.absoluteQuantity;
        this.unit = 'g.'
      }
    },
    getAmount() {
      return this.absoluteQuantity * this.unitValue;
    },
    getUnitValue(unit = 'Kg') {
      let value = 0;

      if (unit === 'Kg') {
        value = this.unitValue * 1e3;
      } else if (unit === 'Ton') {
        value = this.unitValue * 1e6;
      } else if (unit === 'g') {
        value = this.unitValue;
      }

      return {
        value,
        unit: `por ${unit}.`
      }
    }
  }
}