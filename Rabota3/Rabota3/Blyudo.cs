using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Rabota3
{
    internal class Blyudo
    {
        public string Nazvanie;
        public double Ves;
        public int Sebestoimost;
        public int Cena;

        public override string ToString()
        {
            return Nazvanie + ", вес " + Ves + " кг, себестоимость " + Sebestoimost + " руб., цена " + Cena + " руб.";
        }
    }
}
