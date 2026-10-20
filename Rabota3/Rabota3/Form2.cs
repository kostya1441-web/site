using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Rabota3
{
    public partial class Form2 : Form
    {
        public Form2()
        {
            InitializeComponent();
        }

        private void Form2_FormClosing(object sender, FormClosingEventArgs e)
        {
            if (DialogResult == DialogResult.OK)
                try
                {
                    if (Nazv_TB.Text.Trim() == "")
                    {
                        Nazv_TB.Focus();
                        throw new Exception("Вы не ввели название блюда");
                    }
                    if (Ves_TB.Text.Trim() == "")
                    {
                        Ves_TB.Focus();
                        throw new Exception("Вы не ввели вес блюда");
                    }
                    double Ves;
                    try
                    {
                        Ves = Convert.ToDouble(Ves_TB.Text);
                    }
                    catch (FormatException)
                    {
                        Ves_TB.Focus();
                        throw new Exception("Недопустимые символы в строке веса");
                    }
                    if (Ves < 0.1 || Ves > 3.0)
                    {
                        Ves_TB.Focus();
                        throw new Exception("Вес должен быть в диапазоне от 0,1 до 3,0");
                    }
                    if (Seb_TB.Text.Trim() == "")
                    {
                        Seb_TB.Focus();
                        throw new Exception("Вы не ввели себестоимость блюда");
                    }
                    int Seb;
                    try
                    {
                        Seb = Convert.ToInt32(Seb_TB.Text);
                    }
                    catch (FormatException)
                    {
                        Seb_TB.Focus();
                        throw new Exception("Недопустимые символы в строке себестоимости");
                    }
                    if (Seb < 1 || Seb > 5000)
                    {
                        Seb_TB.Focus();
                        throw new Exception("Себестоимость должна быть в диапазоне от 1 до 5000");
                    }
                    if (Cena_TB.Text.Trim() == "")
                    {
                        Cena_TB.Focus();
                        throw new Exception("Вы не ввели цену блюда");
                    }
                    int Cena;
                    try
                    {
                        Cena = Convert.ToInt32(Cena_TB.Text);
                    }
                    catch (FormatException)
                    {
                        Cena_TB.Focus();
                        throw new Exception("Недопустимые символы в строке цены");
                    }
                    if (Cena < 1 || Cena > 10000)
                    {
                        Cena_TB.Focus();
                        throw new Exception("Цена должна быть в диапазоне от 1 до 10000");
                    }
                    if (Cena <= Seb)
                    {
                        Cena_TB.Focus();
                        throw new Exception("Цена должна быть больше себестоимости");
                    }
                }
                catch (Exception Vse)
                {
                    e.Cancel = true;
                    MessageBox.Show(Vse.Message, "Ошибка");
                }
        }
    }
}
