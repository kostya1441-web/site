using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Rabota3
{
    internal class Person
    {
        public string Surname;
        public int BirthYear;
        public string Gender;
        public int Height;

        public override string ToString()
        {
            return Surname + ", " + BirthYear + " г.р., " + Gender + ", рост " + Height + " см";
        }
    }
}
